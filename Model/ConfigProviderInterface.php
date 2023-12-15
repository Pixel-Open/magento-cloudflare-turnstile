<?php
/**
 * Copyright (C) 2023 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PixelOpen\CloudflareTurnstile\Model;

interface ConfigProviderInterface
{
    /**
     * Retrieve assoc array of turnstile configuration
     *
     * @return array
     */
    public function getConfig(): array;
}
