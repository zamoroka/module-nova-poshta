<?php

namespace Zamoroka\NovaPoshta\Controller\Ajax;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Api\FilterBuilder;
use Zamoroka\NovaPoshta\Model\WebApi\NovaPoshtaFactory;

/**
 * Class Cities
 *
 * @package Zamoroka\NovaPoshta\Controller\Ajax
 */
class Cities extends \Magento\Framework\App\Action\Action
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
     */
    public function execute()
    {
        if (!$this->formKeyValidator->validate($this->getRequest())
            || !$this->getRequest()->isAjax()) {
            return '';
        }

        $term = (string)$this->getRequest()->getPost('term');

        return $this->resultJsonFactory->create()->setData(
            json_encode($this->_getSuggestionCitiesArray($term))
        );
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

        $response = $novaPoshtaService->getResponse();

        if ($response['success'] === true) {
            $data = $response['data'][0]['Addresses'];
        }

        return $data;
    }

    /**
     * @param string $term
     * @return array
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
