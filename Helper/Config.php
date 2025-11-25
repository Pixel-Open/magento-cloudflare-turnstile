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
use Magento\Store\Model\ScopeInterface;
use PixelOpen\CloudflareTurnstile\Model\Config\Source\RenderingMode;

class Config extends AbstractHelper
{
    public const TURNSTILE_CONFIG_PATH_SECRET_KEY = 'pixel_open_cloudflare_turnstile/settings/secret_key';
    public const TURNSTILE_CONFIG_PATH_SITEKEY = 'pixel_open_cloudflare_turnstile/settings/sitekey';

    public const TURNSTILE_CONFIG_PATH_FRONTEND_ENABLED = 'pixel_open_cloudflare_turnstile/frontend/enabled';
    public const TURNSTILE_CONFIG_PATH_FRONTEND_THEME = 'pixel_open_cloudflare_turnstile/frontend/theme';
    public const TURNSTILE_CONFIG_PATH_FRONTEND_SIZE = 'pixel_open_cloudflare_turnstile/frontend/size';
    public const TURNSTILE_CONFIG_PATH_FRONTEND_FORMS = 'pixel_open_cloudflare_turnstile/frontend/forms';
    public const TURNSTILE_CONFIG_PATH_FRONTEND_RENDERING_MODE = 'pixel_open_cloudflare_turnstile/frontend/rendering_mode';

    public const TURNSTILE_CONFIG_PATH_ADMINHTML_ENABLED = 'pixel_open_cloudflare_turnstile/adminhtml/enabled';
    public const TURNSTILE_CONFIG_PATH_ADMINHTML_THEME = 'pixel_open_cloudflare_turnstile/adminhtml/theme';
    public const TURNSTILE_CONFIG_PATH_ADMINHTML_SIZE = 'pixel_open_cloudflare_turnstile/adminhtml/size';
    public const TURNSTILE_CONFIG_PATH_ADMINHTML_FORMS = 'pixel_open_cloudflare_turnstile/adminhtml/forms';

    /**
     * Is Turnstile enabled on front
     *
     * @return bool
     */
    public function isEnabledOnFront(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::TURNSTILE_CONFIG_PATH_FRONTEND_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Is Turnstile enabled on admin
     *
     * @return bool
     */
    public function isEnabledOnAdmin(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::TURNSTILE_CONFIG_PATH_ADMINHTML_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve Secret Key
     *
     * @return string
     */
    public function getSecretKey(): string
    {
        return (string)$this->scopeConfig->getValue(
            self::TURNSTILE_CONFIG_PATH_SECRET_KEY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve Sitekey
     *
     * @return string
     */
    public function getSiteKey(): string
    {
        return (string)$this->scopeConfig->getValue(
            self::TURNSTILE_CONFIG_PATH_SITEKEY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve frontend theme
     *
     * @return string
     */
    public function getFrontendTheme(): string
    {
        return (string)$this->scopeConfig->getValue(
            self::TURNSTILE_CONFIG_PATH_FRONTEND_THEME,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve admin theme
     *
     * @return string
     */
    public function getAdminTheme(): string
    {
        return (string)$this->scopeConfig->getValue(
            self::TURNSTILE_CONFIG_PATH_ADMINHTML_THEME,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve frontend size
     *
     * @return string
     */
    public function getFrontendSize(): string
    {
        return (string)$this->scopeConfig->getValue(
            self::TURNSTILE_CONFIG_PATH_FRONTEND_SIZE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve frontend rendering mode
     *
     * @return string
     */
    public function getFrontendRenderingMode(): string
    {
        $mode = $this->scopeConfig->getValue(
            self::TURNSTILE_CONFIG_PATH_FRONTEND_RENDERING_MODE,
            ScopeInterface::SCOPE_STORE
        );

        return $mode ?: RenderingMode::MODE_KNOCKOUT;
    }

    /**
     * Retrieve admin size
     *
     * @return string
     */
    public function getAdminSize(): string
    {
        return (string)$this->scopeConfig->getValue(
            self::TURNSTILE_CONFIG_PATH_ADMINHTML_SIZE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve enabled frontend forms
     *
     * @return string[]
     */
    public function getFrontendForms(): array
    {
        $forms = $this->scopeConfig->getValue(
            self::TURNSTILE_CONFIG_PATH_FRONTEND_FORMS,
            ScopeInterface::SCOPE_STORE
        );

        return $forms ? array_filter(explode(',', $forms)) : [];
    }

    /**
     * Retrieve enabled admin forms
     *
     * @return string[]
     */
    public function getAdminForms(): array
    {
        $forms = $this->scopeConfig->getValue(
            self::TURNSTILE_CONFIG_PATH_ADMINHTML_FORMS,
            ScopeInterface::SCOPE_STORE
        );

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
