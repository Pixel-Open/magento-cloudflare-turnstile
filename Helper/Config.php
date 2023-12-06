<?php
/**
 * Copyright (C) 2023 Pixel Développement
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

    public const TURNSTILE_CONFIG_PATH_FRONTEND_THEME = 'pixel_open_cloudflare_turnstile/frontend/theme';
    public const TURNSTILE_CONFIG_PATH_FRONTEND_SIZE = 'pixel_open_cloudflare_turnstile/frontend/size';
    public const TURNSTILE_CONFIG_PATH_FRONTEND_FORMS = 'pixel_open_cloudflare_turnstile/frontend/forms';

    public const TURNSTILE_CONFIG_PATH_ADMINHTML_THEME = 'pixel_open_cloudflare_turnstile/adminhtml/theme';
    public const TURNSTILE_CONFIG_PATH_ADMINHTML_SIZE = 'pixel_open_cloudflare_turnstile/adminhtml/size';
    public const TURNSTILE_CONFIG_PATH_ADMINHTML_FORMS = 'pixel_open_cloudflare_turnstile/adminhtml/forms';

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
     * Retrieve frontend theme
     *
     * @return string
     */
    public function getFrontendTheme(): string
    {
        return (string)$this->scopeConfig->getValue(self::TURNSTILE_CONFIG_PATH_FRONTEND_THEME);
    }

    /**
     * Retrieve admin theme
     *
     * @return string
     */
    public function getAdminTheme(): string
    {
        return (string)$this->scopeConfig->getValue(self::TURNSTILE_CONFIG_PATH_ADMINHTML_THEME);
    }

    /**
     * Retrieve frontend size
     *
     * @return string
     */
    public function getFrontendSize(): string
    {
        return (string)$this->scopeConfig->getValue(self::TURNSTILE_CONFIG_PATH_FRONTEND_SIZE);
    }

    /**
     * Retrieve admin size
     *
     * @return string
     */
    public function getAdminSize(): string
    {
        return (string)$this->scopeConfig->getValue(self::TURNSTILE_CONFIG_PATH_ADMINHTML_SIZE);
    }

    /**
     * Retrieve enabled frontend forms
     *
     * @return string[]
     */
    public function getFrontendForms(): array
    {
        $forms = $this->scopeConfig->getValue(self::TURNSTILE_CONFIG_PATH_FRONTEND_FORMS);

        return $forms ? array_filter(explode(',', $forms)) : [];
    }

    /**
     * Retrieve enabled admin forms
     *
     * @return string[]
     */
    public function getAdminForms(): array
    {
        $forms = $this->scopeConfig->getValue(self::TURNSTILE_CONFIG_PATH_ADMINHTML_FORMS);

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
