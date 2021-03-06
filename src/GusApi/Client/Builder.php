<?php

namespace GusApi\Client;

use GusApi\Context\Context;
use GusApi\Environment\EnvironmentFactory;

class Builder implements BuilderInterface
{
    /**
     * @var string
     */
    protected $environment;

    /**
     * @var GusApiClient|null
     */
    protected $gusApiClient;

    /**
     * Builder constructor.
     *
     * @param string            $environment
     * @param GusApiClient|null $gusApiClient
     */
    public function __construct(string $environment, ?GusApiClient $gusApiClient = null)
    {
        $this->environment = $environment;
        $this->gusApiClient = $gusApiClient;
    }

    /**
     * @return GusApiClient
     */
    public function build(): GusApiClient
    {
        if (null !== $this->gusApiClient) {
            return $this->gusApiClient;
        }

        $env = EnvironmentFactory::create($this->environment);
        $context = new Context();

        $options = [
            'soap_version' => SOAP_1_2,
            'trace' => true,
            'style' => SOAP_DOCUMENT,
            'stream_context' => $context->getContext(),
            'classmap' => [
                'ZalogujResponse' => \GusApi\Type\Response\LoginResponse::class,
                'WylogujResponse' => \GusApi\Type\Response\LogoutResponse::class,
                'GetValueResponse' => \GusApi\Type\Response\GetValueResponse::class,
                'DaneSzukajResponse' => \GusApi\Type\Response\SearchResponseRaw::class,
                'DanePobierzPelnyRaportResponse' => \GusApi\Type\Response\GetFullReportResponseRaw::class,
            ],
        ];

        $soap = new SoapClient(
            $env->getWSDLUrl(),
            $options
        );

        return new GusApiClient($soap, $env->getServerLocationUrl(), $context);
    }
}
