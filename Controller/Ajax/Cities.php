<?php

namespace Zamoroka\NovaPoshta\Controller\Ajax;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Serialize\Serializer\Json;
use Zamoroka\NovaPoshta\Model\WebApi\NovaPoshtaFactory;

/**
 * Class Cities
 *
 * @package Zamoroka\NovaPoshta\Controller\Ajax
 */
class Cities extends \Magento\Framework\App\Action\Action
{
    private $resultJsonFactory;

    private $resolver;

    private $jsonSerializer;

    private $filterBuilder;

    private $scopeConfig;

    private $httpRequest;

    private $formKeyValidator;

    private $novaPoshtaServiceFactory;

    /**
     * Cities constructor.
     *
     * @param Context                                             $context
     * @param \Magento\Framework\Controller\Result\JsonFactory    $resultJsonFactory
     * @param Resolver                                            $resolver
     * @param \Magento\Framework\Serialize\Serializer\Json        $jsonSerializer
     * @param FilterBuilder                                       $filterBuilder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface  $scopeConfig
     * @param \Magento\Framework\App\Request\Http                 $httpRequest
     * @param \Magento\Framework\Data\Form\FormKey\Validator      $formKeyValidator
     * @param \Zamoroka\NovaPoshta\Model\WebApi\NovaPoshtaFactory $novaPoshtaServiceFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Resolver $resolver,
        Json $jsonSerializer,
        FilterBuilder $filterBuilder,
        ScopeConfigInterface $scopeConfig,
        Http $httpRequest,
        Validator $formKeyValidator,
        NovaPoshtaFactory $novaPoshtaServiceFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resolver = $resolver;
        $this->filterBuilder = $filterBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->jsonSerializer = $jsonSerializer;
        $this->httpRequest = $httpRequest;
        $this->formKeyValidator = $formKeyValidator;
        $this->novaPoshtaServiceFactory = $novaPoshtaServiceFactory;
    }

    /**
     * Index action
     *
     * @throws \Zend_Http_Client_Exception
     */
    public function execute()
    {
        $term = $this->getRequest()->getPost('term');

        if (!$this->formKeyValidator->validate($this->getRequest())
            || !$this->getRequest()->isAjax()) {
            return '';
        }

        $to_json2 = $this->_getSuggestionCitiesArray($term);

        return $this->resultJsonFactory->create()->setData(json_encode($to_json2));
    }

    /**
     * Get cities from api
     *
     * @param string $term
     * @return json
     * @throws \Zend_Http_Client_Exception
     */
    private function _getCitiesFromServer($term = '')
    {
        $data = [];

        /** @var \Zamoroka\NovaPoshta\Model\WebApi\NovaPoshta $novaPoshtaService */
        $novaPoshtaService = $this->novaPoshtaServiceFactory->create();

        $novaPoshtaService->setModelName('Address');
        $novaPoshtaService->setCalledMethod('searchSettlements');
        $novaPoshtaService->setMethodProperties(
            [
                'CityName' => $term,
                'Limit'    => 100
            ]
        );

        $responseJson = $novaPoshtaService->getResponse();
        $response = $this->jsonSerializer->unserialize($responseJson);

        if ($response['success'] === true) {
            $data = $response['data'][0]['Addresses'];
        }

        return $data;
    }

    /**
     * @param $term
     * @return array
     * @throws \Zend_Http_Client_Exception
     */
    private function _getSuggestionCitiesArray($term = '')
    {
        $data = [];

        foreach ($this->_getCitiesFromServer($term) as $city) {
            $data[] = [
                'label' => $city['Present'],
                'value' => $city['Present'],
                'ref'   => $city['Ref']
            ];
        }

        if (count($data) === 0) {
            $data[] = [
                'label' => 'No matches found',
                'value' => 'No matches found'
            ];
        }

        return $data;
    }
}
