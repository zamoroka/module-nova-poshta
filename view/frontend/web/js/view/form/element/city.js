/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define([
    "Magento_Ui/js/form/element/abstract",
    "Magento_Checkout/js/model/quote",
    "mage/url",
    "ko",
    "jquery"
], function (Abstract, quote, url, ko, $) {
    "use strict";

    ko.bindingHandlers.shippingAutoComplete = {
        init: function (element, valueAccessor) {
            var values = valueAccessor();
            var selectedOption = values.selected;

            $(element).autocomplete({
                source: values.options,
                select: function (event, ui) {
                    var selectedItem = ui.item;
                    event.preventDefault();
                    $(element).val(ui.item.label);
                    $(element).trigger("valueUpdate");
                    $(event.target).data("aria-invalid", false);

                    if (typeof ui.item !== "undefined") {
                        selectedOption(ui.item);
                    }

                    quote.shippingAddress().city = $(element).val();

                    var items = [];

                    $.ajax({
                        url: url.build("novaposhta/ajax/warehouses"),
                        type: "POST",
                        data: {
                            "form_key": $.mage.cookies.get("form_key"),
                            "ajax": 1,
                            "cityRef": selectedItem.ref
                        },
                        dataType: "json",
                        async: false,
                        error: function () {
                            console.log("An error have occurred.");
                        },
                        success: function (data) {
                            items = JSON.parse(data);

                            var select = $('[name="shippingAddress.street.0"] select');
                            $(event.target).data("aria-invalid", false);

                            $(select).html("");

                            $(items).each(function (key, item) {
                                $(select).append(new Option(item.label, item.value));
                            });
                        }
                    });

                }
            });
            $(element).attr("autocomplete", "disabledautocomplete");
        }
    };

    return Abstract.extend({
        selectedCity: ko.observable(""),
        postCode: ko.observable(""),
        getCities: function (request, response) {
            var term = request.term;
            var items = [];

            var select = $('[name="shippingAddress.street.shippingAddress.street.0"] select');
            $(select).html("");
            $(select).append(new Option("", ""));

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
            ;
        }
    });
});
