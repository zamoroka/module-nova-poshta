/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define([
    "Magento_Ui/js/form/element/abstract",
    "mage/url",
    "ko",
    "jquery",
    "jquery/ui"
], function (Abstract, url, ko, $) {
    "use strict";

    ko.bindingHandlers.shippingAutoComplete = {
        init: function (element, valueAccessor) {
            var values = valueAccessor();

            $(element).autocomplete({
                source: values.options,
                select: function (event, ui) {
                    var selected = true;
                    // update street address
                    debugger;
                }
            });
        }
    };

    return Abstract.extend({
        selectedCity: ko.observable(""),
        postCode: ko.observable(""),
        getCities: function (request, response) {
            // empty street address
            var value = $('[name="city"]').val();
            var items = JSON.parse('[{"label": "Ужгород", "value": "Ужгород"},{"label": "Ужгород1", "value": "Ужгород1"},{"label": "Ужгород2", "value": "Ужгород2"}]');
            debugger;

            response(items);
        }

    });
});
