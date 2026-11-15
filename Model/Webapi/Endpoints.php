<?php
/**
 * Copyright (C) 2026 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PixelOpen\CloudflareTurnstile\Model\Webapi;

use Magento\Authorization\Model\UserContextInterface;
use PixelOpen\CloudflareTurnstile\Helper\Config;

/**
 * Web API endpoints protected by Cloudflare Turnstile
 */
class Endpoints
{
    protected Config $config;

    /**
     * Protected endpoints: "Service\Class::method" => form code
     *
     * @var string[]
     */
    protected array $endpoints;

    /**
     * @param Config $config
     * @param string[] $endpoints
     */
    public function __construct(
        Config $config,
        array $endpoints = []
    ) {
        $this->config    = $config;
        $this->endpoints = $endpoints;
    }

    /**
     * Test if at least one endpoint can be protected, to avoid useless route matching
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        if (!$this->config->isEnabledOnFront()) {
            return false;
        }

        return (bool)array_intersect(array_values($this->endpoints), $this->config->getFrontendForms());
    }

    /**
     * Test if the API client is authenticated with a token (integration or admin): external services skip Turnstile
     *
     * @param UserContextInterface $userContext
     * @return bool
     */
    public function isTrustedClient(UserContextInterface $userContext): bool
    {
        return in_array(
            (int)$userContext->getUserType(),
            [UserContextInterface::USER_TYPE_INTEGRATION, UserContextInterface::USER_TYPE_ADMIN],
            true
        );
    }

    /**
     * Test if the service method requires a Turnstile validation
     *
     * @param string $serviceClass
     * @param string $serviceMethod
     * @return bool
     */
    public function isProtected(string $serviceClass, string $serviceMethod): bool
    {
        $form = $this->endpoints[ltrim($serviceClass, '\\') . '::' . $serviceMethod] ?? null;

        if ($form === null || !$this->config->isEnabledOnFront()) {
            return false;
        }

        return in_array($form, $this->config->getFrontendForms());
    }
}
