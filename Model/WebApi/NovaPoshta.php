<?php

namespace Zamoroka\NovaPoshta\Model\WebApi;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\HTTP\ZendClientFactory;

/**
 * Class NovaPoshta
 *
 * @package Zamoroka\NovaPoshta\Model\WebApi
 */
class NovaPoshta
{
    const API_URL = 'https://api.novaposhta.ua/v2.0/json/';

    const API_MAX_REDIRECTS = 0;

    const API_TIMEOUT = 30;

    private $scopeConfig;

    private $httpClientFactory;

    private $httpClient;

    /** @var string $apiKey */
    private $apiKey;

    /** @var string $modelName */
    private $modelName;

    /** @var string $calledMethod */
    private $calledMethod;

    /** @var array $methodProperties */
    private $methodProperties;

    /** @var array $requestBody */
    private $requestBody;

    /**
     * NovaPoshta constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\HTTP\ZendClientFactory          $httpClientFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ZendClientFactory $httpClientFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->httpClientFactory = $httpClientFactory;
    }

    /**
     * @return string
     * @throws \Zend_Http_Client_Exception
     */
    public function getResponse()
    {
        $this->getClient()->setRawData(utf8_encode(json_encode($this->getRequestBody())));

        return $this->getClient()->request(\Zend_Http_Client::POST)->getBody();
    }

    /**
     * @return \Magento\Framework\HTTP\ZendClient
     * @throws \Zend_Http_Client_Exception
     */
    private function getClient()
    {
        if (!$this->httpClient) {
            $this->httpClient = $this->httpClientFactory->create();
            $this->httpClient->setUri(self::API_URL);
            $this->httpClient->setConfig(['maxredirects' => self::API_MAX_REDIRECTS, 'timeout' => self::API_TIMEOUT]);
        }

        return $this->httpClient;
    }

    /**
     * @return array
     */
    public function getRequestBody(): array
    {
        if (!$this->requestBody) {
            $this->requestBody = [
                'apiKey'           => $this->getApiKey(),
                'modelName'        => $this->getModelName(),
                'calledMethod'     => $this->getCalledMethod(),
                'methodProperties' => $this->getMethodProperties()
            ];
        }

        return $this->requestBody;
    }

    /**
     * @param array $requestBody
     */
    public function setRequestBody(array $requestBody)
    {
        $this->requestBody = $requestBody;
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        if (!$this->apiKey) {
            $this->apiKey = $this->scopeConfig->getValue('carriers/novaposhta/apikey', ScopeInterface::SCOPE_STORE);
        }

        return $this->apiKey;
    }

    /**
     * @return string
     */
    public function getModelName()
    {
        if (!$this->calledMethod) {
            throw new \LogicException("Pls set Model Name");
        }

        return $this->modelName;
    }

    /**
     * @param string $modelName
     */
    public function setModelName(string $modelName)
    {
        $this->modelName = $modelName;
    }

    /**
     * @return string
     */
    public function getCalledMethod(): string
    {
        if (!$this->calledMethod) {
            throw new \LogicException("Pls set Called Method");
        }

        return $this->calledMethod;
    }

    /**
     * @param string $calledMethod
     */
    public function setCalledMethod(string $calledMethod)
    {
        $this->calledMethod = $calledMethod;
    }

    /**
     * @return array
     */
    public function getMethodProperties(): array
    {
        return $this->methodProperties;
    }

    /**
     * @param array $methodProperties
     */
    public function setMethodProperties(array $methodProperties)
    {
        $this->methodProperties = $methodProperties;
    }
}
