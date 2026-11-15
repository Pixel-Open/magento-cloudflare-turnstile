/**
 * Copyright (C) 2026 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/model/place-order': {
                'PixelOpen_CloudflareTurnstile/js/model/place-order-mixin': true
            }
        }
    }
};
