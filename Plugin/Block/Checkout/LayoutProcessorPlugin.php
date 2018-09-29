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
     * @return array
     */
    public function afterProcess(
        \Mageplaza\Osc\Block\Checkout\LayoutProcessor $subject,
        array $jsLayout
    ) {
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
                    'label' => '',
                ]
            ]
        ];

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['label']
            = __('Warehouse');

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['sortOrder']
            = 60;

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['children'][0]
            = $wareehouseSelect;

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['telephone']['sortOrder']
            = 70;

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['telephone']['config']['additionalClasses']
            = 'street col-mp mp-12 mp-clear';

        return $jsLayout;
    }
}
