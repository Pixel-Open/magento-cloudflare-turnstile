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
                'theme': 'auto',
            },
            action: 'default',
            size: '', // Override config value if not empty
            theme: '', // Override config value if not empty

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
                    const result = turnstile.render(element, {
                        sitekey: this.config.sitekey,
                        theme: this.theme || this.config.theme,
                        size: this.size || this.config.size,
                        action: this.action
                    });
                    if (typeof result === 'undefined') {
                        element.innerText = $.mage.__('Unable to secure the form');
                    }
                }
            }
        });
    }
);
