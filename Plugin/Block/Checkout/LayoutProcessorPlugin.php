<?php

namespace Zamoroka\NovaPoshta\Plugin\Block\Checkout;

use \Magento\Framework\Phrase;

/**
 * Class LayoutProcessor
 *
 * @package Zamoroka\NovaPoshta\Block\Checkout
 */
class LayoutProcessorPlugin
{
    /**
     * @param \Mageplaza\Osc\Block\Checkout\LayoutProcessor $subject
     * @param array                                         $jsLayout
     *
     * @return array
     */
    public function afterProcess(
        \Mageplaza\Osc\Block\Checkout\LayoutProcessor $subject,
        array $jsLayout
    ) {
        /** wareehouse */
        $wareehouseSelect = [
            'component'  => 'Magento_Ui/js/form/element/select',
            'config'     => [
                'customScope' => 'shippingAddress',
                'template'    => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/select',
            ],
            'dataScope'  => 'shippingAddress.street.0',
            'provider'   => 'checkoutProvider',
            'visible'    => true,
            'validation' => [
                'required-entry' => true,
            ],
            'options'    => [
                [
                    'value' => '',
                    'label' => ' --- ',
                ]
            ]
        ];

        $cityField = [
            'component'  => 'Zamoroka_NovaPoshta/js/view/form/element/city',
            'config'     => [
                'customScope'       => 'shippingAddress',
                'template'          => 'ui/form/field',
                'elementTmpl'       => 'Zamoroka_NovaPoshta/form/element/city',
                'additionalClasses' => 'col-mp mp-6'
            ],
            'dataScope'  => 'shippingAddress.city',
            'label'      => new Phrase('City'),
            'provider'   => 'checkoutProvider',
            'sortOrder'  => 4,
            'validation' => [
                'required-entry'  => true,
                'min_text_length' => 1,
                'max_text_length' => 255
            ],
            'options'    => [],
            'visible'    => true,
        ];

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['city'] = $cityField;

        $streetChilds = [$wareehouseSelect];

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['label']
            = __('Warehouse');

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['children'][0] =
            $wareehouseSelect;

        unset(
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['children'][1]
        );
        unset(
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['children'][2]
        );

        return $jsLayout;
    }
}
