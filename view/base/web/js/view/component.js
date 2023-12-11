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
             * Show widget
             */
            render: function (element) {
                if (!this.config.sitekey) {
                    element.innerText = $.mage.__('Unable to secure the form. The sitekey is missing.');
                } else {
                    const widgetId = turnstile.render(element, {
                        sitekey: this.config.sitekey,
                        theme: this.theme || this.config.theme,
                        size: this.size || this.config.size,
                        action: this.action
                    });
                    if (typeof widgetId === 'undefined') {
                        element.innerText = $.mage.__('Unable to secure the form');
                    } else {
                        this.widgetId = widgetId;
                        this.afterRender();
                    }
                }
            },

            /**
             * Reset turnstile
             */
            reset: function () {
                if (this.widgetId) {
                    turnstile.reset(this.widgetId);
                }
            },

            /**
             * After render widget
             */
            afterRender: function () {
                this.ajaxComplete();
            },

            /**
             * Reset turnstile when Ajax request is complete with error
             */
            ajaxComplete: function () {
                $(document).on('ajaxComplete', function (event, xhr) {
                    const result = xhr.responseJSON;
                    if (result.hasOwnProperty('errors')) {
                        if (result.errors) {
                            this.reset();
                        }
                    }
                }.bind(this));
            }
        });
    }
);
