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
        'cfTurnstile',
        'mage/translate'
    ],
    function (
        ko,
        $,
        Component
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'PixelOpen_CloudflareTurnstile/turnstile',
            },
            configSource: 'turnstileConfig',
            config: {
                'sitekey': '',
                'forms': [],
                'size': 'normal',
                'theme': 'auto'
            },
            action: 'default',
            size: '', // Override config value if not empty
            theme: '', // Override config value if not empty,
            widgetId: null,
            autoRender: true,
            element: null,

            /**
             * Initialize
             */
            initialize: function () {
                this._super();

                if (typeof window[this.configSource] !== 'undefined' && window[this.configSource].config) {
                    this.config = window[this.configSource].config;
                }
            },

            /**
             * Can show widget
             *
             * @returns {boolean}
             */
            canShow: function () {
                return this.config.forms.indexOf(this.action) >= 0;
            },

            /**
             * Load widget
             *
             * @param {object} element
             */
            load: function (element) {
                this.element = element;

                if (!this.config.sitekey) {
                    this.element.innerText = $.mage.__('Unable to secure the form. The site key is missing.');
                } else {
                    this.beforeRender();
                    if (this.autoRender) {
                        this.render();
                    }
                }
            },

            /**
             * Render widget
             */
            render: function () {
                const widgetId = turnstile.render(this.element, {
                    sitekey: this.config.sitekey,
                    theme: this.theme || this.config.theme,
                    size: this.size || this.config.size,
                    action: this.action
                });
                if (typeof widgetId === 'undefined') {
                    this.element.innerText = $.mage.__('Unable to secure the form');
                } else {
                    this.widgetId = widgetId;
                }
                this.afterRender();
            },

            /**
             * Before render widget
             */
            beforeRender: function () {},

            /**
             * After render widget
             */
            afterRender: function () {},

            /**
             * Reset widget
             */
            reset: function () {
                if (this.widgetId) {
                    turnstile.reset(this.widgetId);
                }
            }
        });
    }
);
