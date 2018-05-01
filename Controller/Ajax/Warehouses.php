<?php

namespace Zamoroka\NovaPoshta\Controller\Ajax;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Api\FilterBuilder;
use Zamoroka\NovaPoshta\Model\WebApi\NovaPoshtaFactory;

/**
 * Class Departments
 *
 * @package Zamoroka\NovaPoshta\Controller\Ajax
 */
class Warehouses extends \Magento\Framework\App\Action\Action
{
    private $resultJsonFactory;

    private $filterBuilder;

    private $formKeyValidator;

    private $novaPoshtaServiceFactory;

    /**
     * Cities constructor.
     *
     * @param Context                                             $context
     * @param \Magento\Framework\Controller\Result\JsonFactory    $resultJsonFactory
     * @param FilterBuilder                                       $filterBuilder
     * @param \Magento\Framework\Data\Form\FormKey\Validator      $formKeyValidator
     * @param \Zamoroka\NovaPoshta\Model\WebApi\NovaPoshtaFactory $novaPoshtaServiceFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        FilterBuilder $filterBuilder,
        Validator $formKeyValidator,
        NovaPoshtaFactory $novaPoshtaServiceFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->filterBuilder = $filterBuilder;
        $this->formKeyValidator = $formKeyValidator;
        $this->novaPoshtaServiceFactory = $novaPoshtaServiceFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\Result\Json|string
     * @throws \Zend_Http_Client_Exception
     */
    public function execute()
    {
        if (!$this->formKeyValidator->validate($this->getRequest())
            || !$this->getRequest()->isAjax()) {
            return '';
        }

        $cityRef = (string)$this->getRequest()->getPost('cityRef');
        $warehouses = $this->_getWarehousesOptionArray($cityRef);

        return $this->resultJsonFactory->create()->setData(json_encode($warehouses));
    }

    /**
     * Get cities from api
     *
     * @param string $cityRef
     * @return array
     * @throws \Zend_Http_Client_Exception
     */
    private function _getWarehousesFromServer($cityRef = '')
    {
        $data = [];

        /** @var \Zamoroka\NovaPoshta\Model\WebApi\NovaPoshta $novaPoshtaService */
        $novaPoshtaService = $this->novaPoshtaServiceFactory->create();

        $novaPoshtaService->setModelName('AddressGeneral');
        $novaPoshtaService->setCalledMethod('getWarehouses');
        $novaPoshtaService->setMethodProperties(['CityRef' => $cityRef]);

        $response = $novaPoshtaService->getResponse();

        if ($response['success'] === true) {
            $data = $response['data'];
        }

        return $data;
    }

    /**
     * @param string $cityRef
     * @return array
     * @throws \Zend_Http_Client_Exception
     */
    private function _getWarehousesOptionArray($cityRef = '')
    {
        $data = [
            [
                'label' => '---',
                'value' => ''
            ]
        ];

        foreach ($this->_getWarehousesFromServer($cityRef) as $city) {
            $data[] = [
                'label' => $city['Description'],
                'value' => $city['Description']
            ];
        };

        return $data;
    }
}
