<?php
/**
 * Copyright (C) 2022 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PixelOpen\CloudflareTurnstile\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Theme implements OptionSourceInterface
{
    public const THEME_AUTO = 'auto';
    public const THEME_LIGHT = 'light';
    public const THEME_DARK = 'dark';

    /**
     * Get options as array
     *
     * @return string[][]
     */
    public function toOptionArray(): array
    {
        $options = [];

        foreach ($this->toArray() as $value) {
            $options[] = [
                'value' => $value,
                'label' => $value,
            ];
        }

        return $options;
    }

    /**
     * Get options as array
     *
     * @return string[]
     */
    public function toArray(): array
    {
        return [self::THEME_AUTO, self::THEME_LIGHT, self::THEME_DARK];
    }
}
