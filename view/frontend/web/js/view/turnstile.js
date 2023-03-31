/**
 * Copyright (C) 2023 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*global define*/
define(
    [
        'ko',
        'jquery',
        'uiComponent',
        'Magento_Customer/js/customer-data',
        'cfTurnstile',
        'mage/translate'
    ],
    function (
        ko,
        $,
        Component,
        customerData
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'PixelOpen_CloudflareTurnstile/turnstile',
            },
            customer: customerData.get('customer'),
            configSource: 'checkout',
            turnstile: {
                'sitekey': '',
                'theme': 'auto',
                'forms': []
            },
            action: 'default',

            /**
             * Initialize
             */
            initialize: function () {
                this._super();

                if (typeof window[this.configSource] !== 'undefined' && window[this.configSource].turnstile) {
                    this.turnstile = window[this.configSource].turnstile;
                }
            },

            /**
             * Can show message
             *
             * @returns {boolean}
             */
            canShow: function () {
                return !this.customer().firstname && this.turnstile.forms.indexOf(this.action) >= 0;
            },

            /**
             * Show message
             */
            render: function (element) {
                if (!this.turnstile.sitekey) {
                    element.innerText = $.mage.__('Unable to secure the form. The sitekey is missing.');
                } else {
                    const result = turnstile.render(element, {
                        sitekey: this.turnstile.sitekey,
                        theme: this.turnstile.theme,
                        action: this.turnstile.action
                    });
                    if (typeof result === 'undefined') {
                        element.innerText = $.mage.__('Unable to secure the form');
                    }
                }
            }
        });
    }
);
