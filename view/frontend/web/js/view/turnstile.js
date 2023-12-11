/**
 * Copyright (C) 2023 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*global define*/
define(
    [
        'PixelOpen_CloudflareTurnstile/js/view/component',
        'Magento_Customer/js/customer-data'
    ],
    function (
        Component,
        customerData
    ) {
        'use strict';

        return Component.extend({
            customer: customerData.get('customer'),

            /**
             * Can show widget
             *
             * @returns {boolean}
             */
            canShow: function () {
                if (this.customer().firstname) {
                    return false;
                }

                return this._super();
            }
        });
    }
);
