/**
 * Copyright (C) 2026 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*global define*/
define(
    [
        'mage/utils/wrapper',
        'PixelOpen_CloudflareTurnstile/js/model/checkout'
    ],
    function (
        wrapper,
        checkoutTurnstile
    ) {
        'use strict';

        /**
         * Send the Turnstile response with the place order request
         */
        return function (placeOrder) {
            return wrapper.wrap(placeOrder, function (originalAction, serviceUrl, payload, messageContainer) {
                const component = checkoutTurnstile.component;

                if (!component || !component.isRequired()) {
                    return originalAction(serviceUrl, payload, messageContainer);
                }

                payload['cf-turnstile-response'] = component.getResponse();

                return originalAction(serviceUrl, payload, messageContainer).fail(function () {
                    // A token can only be validated once
                    component.reset();
                });
            });
        };
    }
);
