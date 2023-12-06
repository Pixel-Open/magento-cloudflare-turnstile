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

class Size implements OptionSourceInterface
{
    public const SIZE_NORMAL = 'normal';
    public const SIZE_COMPACT = 'compact';

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
        return [self::SIZE_NORMAL, self::SIZE_COMPACT];
    }
}
