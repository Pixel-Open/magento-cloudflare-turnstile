<?php
/**
 * Copyright (C) 2026 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PixelOpen\CloudflareTurnstile\Plugin\Webapi;

use Exception;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Webapi\Exception as WebapiException;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Webapi\Controller\Rest\RequestValidator;
use Magento\Webapi\Controller\Rest\Router;
use PixelOpen\CloudflareTurnstile\Model\Validator;
use PixelOpen\CloudflareTurnstile\Model\Webapi\Endpoints;
use Psr\Log\LoggerInterface;

/**
 * Validate Cloudflare Turnstile on protected REST endpoints (synchronous and asynchronous requests)
 */
class RestValidation
{
    public const RESPONSE_PARAM = 'cf-turnstile-response';
    public const RESPONSE_HEADER = 'X-Turnstile-Response';

    protected Request $request;

    protected Router $router;

    protected Validator $validator;

    protected Endpoints $endpoints;

    protected UserContextInterface $userContext;

    protected LoggerInterface $logger;

    /**
     * @param Request $request
     * @param Router $router
     * @param Validator $validator
     * @param Endpoints $endpoints
     * @param UserContextInterface $userContext
     * @param LoggerInterface $logger
     */
    public function __construct(
        Request $request,
        Router $router,
        Validator $validator,
        Endpoints $endpoints,
        UserContextInterface $userContext,
        LoggerInterface $logger
    ) {
        $this->request     = $request;
        $this->router      = $router;
        $this->validator   = $validator;
        $this->endpoints   = $endpoints;
        $this->userContext = $userContext;
        $this->logger      = $logger;
    }

    /**
     * Validate Cloudflare Turnstile after native request validation (route, ACL, HTTPS)
     *
     * @param RequestValidator $subject
     * @param callable $proceed
     * @return void
     * @throws WebapiException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundValidate(RequestValidator $subject, callable $proceed): void
    {
        if (!$this->endpoints->isEnabled() || $this->endpoints->isTrustedClient($this->userContext)) {
            $proceed();
            return;
        }

        $proceed();
        $route = $this->router->match($this->request);

        if (!$this->endpoints->isProtected($route->getServiceClass(), $route->getServiceMethod())) {
            return;
        }

        try {
            $isValid = $this->validator->isValid($this->getCfResponse());
        } catch (Exception $exception) {
            $this->logger->error('Cloudflare Turnstile Web API validation: ' . $exception->getMessage());
            throw new WebapiException(
                __('Security validation error: %1', __('unable to contact Cloudflare to validate the form.'))
            );
        }

        if (!$isValid) {
            throw new WebapiException(
                __('Security validation error: %1', join(', ', $this->validator->getErrorMessages()))
            );
        }
    }

    /**
     * Retrieve Cloudflare Turnstile response from the request body, or from the header for headless integrations
     *
     * @return string|null
     */
    protected function getCfResponse(): ?string
    {
        try {
            $response = $this->request->getBodyParams()[self::RESPONSE_PARAM] ?? null;
        } catch (Exception $exception) {
            $response = null;
        }

        if (!$response) {
            $response = $this->request->getHeader(self::RESPONSE_HEADER) ?: null;
        }

        return is_string($response) ? $response : null;
    }
}
