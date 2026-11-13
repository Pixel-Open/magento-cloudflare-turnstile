/**
 * Copyright (C) 2023 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*global define*/
define(
    [
        'jquery',
        'PixelOpen_CloudflareTurnstile/js/view/component',
        'Magento_Customer/js/customer-data',
        'Magento_Customer/js/action/login'
    ],
    function (
        $,
        Component,
        customerData,
        loginAction
    ) {
        'use strict';

        return Component.extend({
            customer: customerData.get('customer'),
            authentication: '.authentication-dropdown, .popup-authentication',

            /**
             * Initialize
             */
            initialize: function () {
                this._super();

                if (this.action === 'login-ajax') {
                    // A token can only be validated once: reset the widget after each login attempt
                    loginAction.registerLoginCallback(this.reset.bind(this));
                }
            },

            /**
             * Can show widget
             *
             * @returns {boolean}
             */
            canShow: function () {
                if (this.customer().hasOwnProperty('firstname') && this.customer().firstname) {
                    // Widget is disabled when the customer is logged in
                    return false;
                }

                return this._super();
            },

            /**
             * Before Render
             */
            beforeRender: function () {
                if (this.action === 'login-ajax' && !this.autoRendering) {
                    this.loginAjax();

                    const cart = customerData.get('cart');
                    cart.subscribe(function (cartData) {
                        if (cartData.isGuestCheckoutAllowed === false) {
                            this.loginAjax();
                        }
                    }, this);
                }

                this._super();
            },

            /**
             * Render widget only when modal is open
             */
            loginAjax: function () {
                $(this.authentication).off('transitionend').on('transitionend', function (event) {
                    const target = $(event.target);
                    target.find('.cf-turnstile').empty();
                    if (target.hasClass('_show')) {
                        this.render();
                    }
                }.bind(this));
            }
        });
    }
);
