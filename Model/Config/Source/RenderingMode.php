<?php
/**
 * Copyright (C) 2023 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PixelOpen\CloudflareTurnstile\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class RenderingMode implements OptionSourceInterface
{
    public const MODE_KNOCKOUT = 'knockout';

    public const MODE_FALLBACK = 'fallback';

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::MODE_KNOCKOUT,
                'label' => __('Knockout (Luma/Blank themes)'),
            ],
            [
                'value' => self::MODE_FALLBACK,
                'label' => __('Hyvä / Alpine fallback'),
            ],
        ];
    }
}

