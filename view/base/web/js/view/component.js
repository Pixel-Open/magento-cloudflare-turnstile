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
                'enabled': false,
                'sitekey': '',
                'forms': [],
                'size': 'normal',
                'theme': 'auto',
                'renderingMode': 'knockout'
            },
            action: 'default',
            size: '', // Override config value if not empty
            theme: '', // Override config value if not empty,
            widgetId: null,
            autoRendering: true,
            element: null,
            renderingMode: 'knockout',

            /**
             * Initialize
             */
            initialize: function () {
                this._super();

                if (typeof window[this.configSource] !== 'undefined' && window[this.configSource].config) {
                    this.config = window[this.configSource].config;
                }

                this.renderingMode = this.config.renderingMode || 'knockout';
            },

            /**
             * Can show widget
             *
             * @returns {boolean}
             */
            canShow: function () {
                return this.config.enabled && this.config.forms.indexOf(this.action) >= 0;
            },

            /**
             * Load widget
             *
             * @param {object} element
             */
            load: function (element) {
                this.element = element;

                // Extract sitekey value from observable if needed
                const sitekey = this.getValue(this.config.sitekey);
                
                if (sitekey) {
                    this.beforeRender();
                    if (this.autoRendering) {
                        this.render();
                    }
                    return;
                }

                this.element.innerText = $.mage.__('Unable to secure the form. The site key is missing.');
            },

            /**
             * Get value from observable or return value directly
             * 
             * @param {*} value
             * @returns {*}
             */
            getValue: function (value) {
                if (ko?.isObservable(value)) {
                    return ko.unwrap(value);
                }
                return value;
            },

            /**
             * Render widget
             */
            render: function () {
                if (this.element) {
                    // Extract values from observables if needed
                    let sitekey = this.getValue(this.config.sitekey);
                    const theme = String(this.getValue(this.theme || this.config.theme) || 'auto').trim();
                    const size = String(this.getValue(this.size || this.config.size) || 'normal').trim();
                    const action = String(this.getValue(this.action) || 'default').trim();
                    
                    // Validate and convert sitekey to string
                    if (!sitekey) {
                        this.element.innerText = $.mage.__('Unable to secure the form. The site key is missing.');
                        return;
                    }
                    
                    sitekey = String(sitekey).trim();
                    
                    if (sitekey === '' || sitekey === 'null' || sitekey === 'undefined') {
                        this.element.innerText = $.mage.__('Unable to secure the form. The site key is invalid.');
                        return;
                    }
                    
                    const renderConfig = {
                        sitekey: sitekey,
                        theme: theme,
                        size: size,
                        action: action
                    };
                    
                    const widgetId = turnstile.render(this.element, renderConfig);
                    if (typeof widgetId === 'undefined') {
                        this.element.innerText = $.mage.__('Unable to secure the form');
                    } else {
                        this.widgetId = widgetId;
                    }
                    this.afterRender();
                }
            },

            /**
             * Before render widget
             */
            beforeRender: function () {
                // Do something before rendering the widget
            },

            /**
             * After render widget
             */
            afterRender: function () {
                // Do something after rendering the widget
            },

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
