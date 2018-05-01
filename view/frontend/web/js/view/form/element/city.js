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
    "jquery"
], function (Abstract, url, ko, $) {
    "use strict";

    var City = {
        getCities: function (request, response) {
            var term = request.term;
            var items = [];

            $(this.element).data("valid", false);
            if (term.length > 1 && term.length < 30) {
                $.ajax({
                    url: url.build("novaposhta/ajax/cities"),
                    type: "POST",
                    data: {
                        "form_key": $.mage.cookies.get("form_key"),
                        "ajax": 1,
                        "term": term
                    },
                    dataType: "json",
                    async: false,
                    error: function () {
                        console.log("An error have occurred.");
                        response(items);
                    },
                    success: function (data) {
                        items = JSON.parse(data);
                        response(items);
                    }
                });
            }
        },

        getWarehouses: function (cityRef) {
            var items = [];

            $.ajax({
                url: url.build("novaposhta/ajax/warehouses"),
                type: "POST",
                data: {
                    "form_key": $.mage.cookies.get("form_key"),
                    "ajax": 1,
                    "cityRef": cityRef
                },
                dataType: "json",
                async: false,
                error: function () {
                    console.log("An error have occurred.");
                },
                success: function (data) {
                    items = JSON.parse(data);

                    var select = $('[name="shippingAddress.street.shippingAddress.street.0"] select');
                    $(select).html("");

                    $(items).each(function (key, item) {
                        $(select).append(new Option(item.value, item.label));
                    });


                    debugger;

                }
            });
        }
    };
    ko.bindingHandlers.shippingAutoComplete = {
        init: function (element, valueAccessor) {
            var values = valueAccessor();

            $(element).autocomplete({
                source: values.options,
                select: function (event, ui) {
                    $(event.target).data("valid", true);

                    var selecteItem = ui.item;
                    var warehouses = City.getWarehouses(selecteItem.ref);

                    debugger;
                    // update street address
                }
            });
        }
    };

    return Abstract.extend({
        selectedCity: ko.observable(""),
        postCode: ko.observable(""),
        getCities: function (request, response) {
            City.getCities(request, response);
        }
    });
});
