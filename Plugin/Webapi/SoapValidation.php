<?php
/**
 * Copyright (C) 2026 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PixelOpen\CloudflareTurnstile\Plugin\Webapi;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Webapi\Exception as WebapiException;
use Magento\Framework\Webapi\Request;
use Magento\Webapi\Controller\Soap\Request\Handler;
use Magento\Webapi\Model\Soap\Config as SoapConfig;
use PixelOpen\CloudflareTurnstile\Model\Webapi\Endpoints;

/**
 * Protected endpoints cannot be validated with SOAP: they are restricted to authenticated clients
 */
class SoapValidation
{
    protected Request $request;

    protected SoapConfig $soapConfig;

    protected UserContextInterface $userContext;

    protected Endpoints $endpoints;

    /**
     * @param Request $request
     * @param SoapConfig $soapConfig
     * @param UserContextInterface $userContext
     * @param Endpoints $endpoints
     */
    public function __construct(
        Request $request,
        SoapConfig $soapConfig,
        UserContextInterface $userContext,
        Endpoints $endpoints
    ) {
        $this->request     = $request;
        $this->soapConfig  = $soapConfig;
        $this->userContext = $userContext;
        $this->endpoints   = $endpoints;
    }

    /**
     * Block protected operations for anonymous clients
     *
     * @param Handler $subject
     * @param string $operation
     * @param array $arguments
     * @return void
     * @throws WebapiException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function before__call(Handler $subject, $operation, $arguments): void
    {
        if (!$this->endpoints->isEnabled()) {
            return;
        }

        $operationInfo = $this->soapConfig->getServiceMethodInfo($operation, $this->request->getRequestedServices());

        if (!$this->endpoints->isProtected((string)$operationInfo['class'], (string)$operationInfo['method'])) {
            return;
        }

        if (!$this->endpoints->isTrustedClient($this->userContext)) {
            throw new WebapiException(__('Operation is available only to authenticated clients'));
        }
    }
}
