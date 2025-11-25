<?php
/**
 * Copyright (C) 2023 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PixelOpen\CloudflareTurnstile\Block;

use PixelOpen\CloudflareTurnstile\Helper\Config;
use PixelOpen\CloudflareTurnstile\Model\Config\Source\RenderingMode;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Filter\FilterManager;

class Turnstile extends Template
{
    /**
     * Path to template file in theme.
     *
     * @var string $_template
     */
    protected $_template = 'PixelOpen_CloudflareTurnstile::turnstile.phtml';

    protected FilterManager $filter;

    protected Config $config;

    /**
     * @param Context $context
     * @param FilterManager $filter
     * @param Config $config
     * @param mixed[] $data
     */
    public function __construct(
        Context $context,
        FilterManager $filter,
        Config $config,
        array $data = []
    ) {
        $this->filter = $filter;
        $this->config = $config;

        parent::__construct($context, $data);
    }

    /**
     * Retrieve action
     *
     * @return string
     */
    public function getAction(): string
    {
        return $this->getData('action') ?: $this->config->getAction();
    }

    /**
     * Retrieve size, will override the config if set for block in layout
     *
     * @return string|null
     */
    public function getSize(): ?string
    {
        return $this->getData('size');
    }

    /**
     * Retrieve theme, will override the config if set for block in layout
     *
     * @return string|null
     */
    public function getTheme(): ?string
    {
        return $this->getData('theme');
    }

    /**
     * Retrieve id
     *
     * @return string
     */
    public function getId(): string
    {
        return 'cloudflare-turnstile-' . $this->filter->translitUrl($this->getAction());
    }

    /**
     * Check if Turnstile is enabled on frontend
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->config->isEnabledOnFront();
    }

    /**
     * Retrieve sitekey
     *
     * @return string
     */
    public function getSiteKey(): string
    {
        return $this->config->getSiteKey();
    }

    /**
     * Check if the current action/form is enabled
     *
     * @return bool
     */
    public function isFormEnabled(): bool
    {
        $forms = $this->config->getFrontendForms();
        return in_array($this->getAction(), $forms);
    }

    /**
     * Retrieve theme from config if not overridden
     *
     * @return string
     */
    public function getThemeFromConfig(): string
    {
        return $this->getTheme() ?: $this->config->getFrontendTheme();
    }

    /**
     * Retrieve size from config if not overridden
     *
     * @return string
     */
    public function getSizeFromConfig(): string
    {
        return $this->getSize() ?: $this->config->getFrontendSize();
    }

    /**
     * Retrieve configured rendering mode
     *
     * @return string
     */
    public function getRenderingMode(): string
    {
        return $this->config->getFrontendRenderingMode();
    }

    /**
     * Use Knockout rendering (Luma/Blank themes)
     *
     * @return bool
     */
    public function isKnockoutRendering(): bool
    {
        return $this->getRenderingMode() === RenderingMode::MODE_KNOCKOUT;
    }

    /**
     * Use Hyvä/Alpine fallback rendering
     *
     * @return bool
     */
    public function isFallbackRendering(): bool
    {
        return $this->getRenderingMode() === RenderingMode::MODE_FALLBACK;
    }
}
