/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [],
    function () {
        "use strict";
        return {
            getRules: function () {
                return {
                    "postcode": {
                        "required": false,
                        "visible": false
                    },
                    "city": {
                        "required": true
                    },
                    "country_id": {
                        "visible": false
                    }
                };
            }
        };
    }
);
