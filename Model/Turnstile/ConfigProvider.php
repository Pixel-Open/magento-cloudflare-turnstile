<?php
/**
 * Copyright (C) 2022 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PixelOpen\CloudflareTurnstile\Model\Turnstile;

use PixelOpen\CloudflareTurnstile\Helper\Config;
use Magento\Checkout\Model\ConfigProviderInterface;

class ConfigProvider implements ConfigProviderInterface
{
    protected Config $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function getConfig(): array
    {
        return [
            'turnstile' => [
                'sitekey' => $this->config->getSitekey(),
                'theme'   => $this->config->getTheme(),
                'forms'   => $this->config->getForms(),
            ]
        ];
    }
}
