<?php
/**
 * Copyright (C) 2022 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PixelOpen\CloudflareTurnstile\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper
{
    public const TURNSTILE_CONFIG_PATH_ENABLED = 'pixel_open_cloudflare_turnstile/settings/enabled';
    public const TURNSTILE_CONFIG_PATH_SECRET_KEY = 'pixel_open_cloudflare_turnstile/settings/secret_key';
    public const TURNSTILE_CONFIG_PATH_SITEKEY = 'pixel_open_cloudflare_turnstile/settings/sitekey';
    public const TURNSTILE_CONFIG_PATH_THEME = 'pixel_open_cloudflare_turnstile/settings/theme';
    public const TURNSTILE_CONFIG_PATH_FORMS = 'pixel_open_cloudflare_turnstile/settings/forms';

    /**
     * Is Turnstile enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::TURNSTILE_CONFIG_PATH_ENABLED);
    }

    /**
     * Retrieve Secret Key
     *
     * @return string
     */
    public function getSecretKey(): string
    {
        return (string)$this->scopeConfig->getValue(self::TURNSTILE_CONFIG_PATH_SECRET_KEY);
    }

    /**
     * Retrieve Sitekey
     *
     * @return string
     */
    public function getSitekey(): string
    {
        return (string)$this->scopeConfig->getValue(self::TURNSTILE_CONFIG_PATH_SITEKEY);
    }

    /**
     * Retrieve theme
     *
     * @return string
     */
    public function getTheme(): string
    {
        return (string)$this->scopeConfig->getValue(self::TURNSTILE_CONFIG_PATH_THEME);
    }

    /**
     * Retrieve enabled forms
     *
     * @return string[]
     */
    public function getForms(): array
    {
        $forms = $this->scopeConfig->getValue(self::TURNSTILE_CONFIG_PATH_FORMS);

        return $forms ? array_filter(explode(',', $forms)) : [];
    }

    /**
     * Retrieve API URL
     *
     * @return string
     */
    public function getApiUrl(): string
    {
        return 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
    }

    /**
     * Retrieve default action
     *
     * @return string
     */
    public function getAction(): string
    {
        return 'default';
    }
}
