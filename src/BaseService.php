<?php
namespace PHPSC\PagSeguro;

use PHPSC\PagSeguro\Credentials;
use SimpleXMLElement;

abstract class BaseService
{
    /**
     * @var string
     */
    const HOST = 'ws.pagseguro.uol.com.br';

    /**
     * @var string
     */
    const SANDBOX_HOST = 'ws.sandbox.pagseguro.uol.com.br';

    /**
     * @var Credentials
     */
    private $credentials;

    /**
     * @var Client
     */
    private $client;

    /**
     * @param Credentials $credentials
     * @param Client $client
     */
    public function __construct(
        Credentials $credentials,
        Client $client = null
    ) {
        $this->credentials = $credentials;
        $this->client = $client ?: new Client();
    }

    /**
     * @return boolean
     */
    public function isSandbox()
    {
        return $this->credentials->isSandbox();
    }

    /**
     * @param string $resource
     * @param array $params
     *
     * @return string
     */
    public function buildUri($resource, array $params = array())
    {
        $params = array_merge(
            $params,
            array(
                'email' => $this->credentials->getEmail(),
                'token' => $this->credentials->getToken()
            )
        );

        return $this->getBaseUri($resource) . '?' . http_build_query($params);
    }

    /**
     * @param string $resource
     *
     * @return string
     */
    protected function getBaseUri($resource)
    {
        if ($this->isSandbox()) {
            return 'https://' . static::SANDBOX_HOST . $resource;
        }

        return 'https://' . static::HOST . $resource;
    }

    /**
     * @param string $resource
     * @param array $params
     *
     * @return SimpleXMLElement
     */
    protected function get($resource, array $params = array())
    {
        return $this->client->get($this->buildUri($resource, $params));
    }

    /**
     * @param string $resource
     * @param SimpleXMLElement $request
     *
     * @return SimpleXMLElement
     */
    protected function post($resource, SimpleXMLElement $request)
    {
        return $this->client->post($this->buildUri($resource), $request);
    }
}
