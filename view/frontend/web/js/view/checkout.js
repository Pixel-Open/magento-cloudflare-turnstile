/**
 * Copyright (C) 2026 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*global define*/
define(
    [
        'jquery',
        'PixelOpen_CloudflareTurnstile/js/view/component',
        'PixelOpen_CloudflareTurnstile/js/model/checkout',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/model/customer',
        'mage/translate'
    ],
    function (
        $,
        Component,
        checkoutTurnstile,
        quote,
        customer
    ) {
        'use strict';

        /**
         * The template is rendered in the "before-place-order" region of each payment method.
         * A single widget is rendered, in the placeholder of the selected payment method.
         */
        return Component.extend({
            defaults: {
                template: 'PixelOpen_CloudflareTurnstile/checkout',
                skipPayments: {} // Payment methods not placing the order with the Web API: {"method_code": true}
            },
            action: 'checkout',
            autoRendering: false,
            paymentMethodSelector: '.payment-method._active',
            placeholderSelector: '.cf-turnstile-checkout',
            toolbarSelector: '.actions-toolbar',

            /**
             * Initialize
             */
            initialize: function () {
                this._super();

                checkoutTurnstile.component = this;
                quote.paymentMethod.subscribe(this.refresh, this);
            },

            /**
             * Can show widget: the place order request is validated for guests only
             *
             * @returns {boolean}
             */
            canShow: function () {
                if (customer.isLoggedIn()) {
                    return false;
                }

                return this._super();
            },

            /**
             * Is the widget required for the selected payment method
             *
             * @returns {boolean}
             */
            isRequired: function () {
                const method = quote.paymentMethod();

                if (!this.canShow() || !method) {
                    return false;
                }

                return (this.skipPayments || {})[method.method] !== true;
            },

            /**
             * Move the widget to the selected payment method, once the DOM is updated
             */
            refresh: function () {
                setTimeout(this.move.bind(this), 0);
            },

            /**
             * Render the widget in the selected payment method
             */
            move: function () {
                if (!this.isRequired()) {
                    this.remove();
                    this.element = null;
                    return;
                }

                const target = this.getTarget();

                if (!target || target === this.element) {
                    return;
                }

                this.remove();
                this.element = target;

                if (!this.config.sitekey) {
                    this.element.innerText = $.mage.__('Unable to secure the form. The site key is missing.');
                    return;
                }

                this.render();
            },

            /**
             * Retrieve the placeholder of the selected payment method
             *
             * @returns {HTMLElement|null}
             */
            getTarget: function () {
                const method = $(this.paymentMethodSelector).first();

                if (!method.length) {
                    return null;
                }

                let target = method.find(this.placeholderSelector).first();

                if (!target.length) {
                    // Payment method template without the "before-place-order" region
                    const toolbar = method.find(this.toolbarSelector).first();

                    if (!toolbar.length) {
                        return null;
                    }
                    target = $('<div class="cf-turnstile cf-turnstile-checkout"></div>').insertBefore(toolbar);
                }

                return target.get(0);
            },

            /**
             * Remove the current widget
             */
            remove: function () {
                if (this.widgetId !== null && typeof turnstile !== 'undefined') {
                    try {
                        turnstile.remove(this.widgetId);
                    } catch (e) {
                        // The widget has already been removed from the DOM
                    }
                }
                this.widgetId = null;
            },

            /**
             * Retrieve the widget response token
             *
             * @returns {string|null}
             */
            getResponse: function () {
                if (this.widgetId === null || typeof turnstile === 'undefined') {
                    return null;
                }

                return turnstile.getResponse(this.widgetId) || null;
            }
        });
    }
);
