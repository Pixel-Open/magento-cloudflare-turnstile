<?php
/**
 * Copyright (C) 2023 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PixelOpen\CloudflareTurnstile\Model\ConfigProvider;

use PixelOpen\CloudflareTurnstile\Helper\Config;
use PixelOpen\CloudflareTurnstile\Model\ConfigProviderInterface;

class Adminhtml implements ConfigProviderInterface
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
            'config' => [
                'enabled' => $this->config->isEnabledOnAdmin(),
                'sitekey' => $this->config->getSiteKey(),
                'theme'   => $this->config->getAdminTheme(),
                'size'    => $this->config->getAdminSize(),
                'forms'   => $this->config->getAdminForms(),
            ]
        ];
    }
}
