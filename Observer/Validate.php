<?php
/**
 * Copyright (C) 2023 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PixelOpen\CloudflareTurnstile\Observer;

use Exception;
use JetBrains\PhpStorm\NoReturn;
use Magento\Customer\Controller\Ajax\Login as AjaxLoginPost;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\Http as Request;
use Magento\Framework\App\Response\Http as Response;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Serialize\Serializer\Json;
use PixelOpen\CloudflareTurnstile\Helper\Config;
use PixelOpen\CloudflareTurnstile\Model\Validator;

abstract class Validate implements ObserverInterface
{
    protected ManagerInterface $messageManager;

    protected Response $response;

    protected Validator $validator;

    protected Json $json;

    protected Config $config;

    protected array $actions = [];

    /**
     * @param ManagerInterface $messageManager
     * @param Response $response
     * @param Validator $validator
     * @param Json $json
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        ManagerInterface $messageManager,
        Response $response,
        Validator $validator,
        Json $json,
        Config $config,
        array $data = []
    ) {
        $this->messageManager = $messageManager;
        $this->response       = $response;
        $this->validator      = $validator;
        $this->json           = $json;
        $this->config         = $config;
        $this->actions        = $data['actions'] ?? [];
    }

    /**
     * Validate Cloudflare Turnstile
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        /** @var ActionInterface $action */
        $action = $observer->getEvent()->getData('controller_action');
        /** @var Request $request */
        $request = $observer->getEvent()->getData('request');

        if ($this->canValidate($request, $action)) {
            try {
                if (!$this->validator->isValid($this->getCfResponse($request, $action))) {
                    $this->persist($request, $action);
                    $this->error(
                        $request,
                        $action,
                        __('Security validation error: %1', join(', ', $this->validator->getErrorMessages()))
                    );
                }
            } catch (Exception $exception) {
                $this->error($request, $action, __('Security validation error: %1', $exception->getMessage()));
            }
        }
    }

    /**
     * Retrieve Cloudflare Turnstile response
     *
     * @param Request $request
     * @param ActionInterface $action
     *
     * @return string|null
     */
    public function getCfResponse(Request $request, ActionInterface $action): ?string
    {
        return $request->getParam('cf-turnstile-response');
    }

    /**
     * Send error
     *
     * @param Request $request
     * @param ActionInterface $action
     * @param Phrase $message
     * @return void
     */
    protected function error(Request $request, ActionInterface $action, Phrase $message): void
    {
        $this->messageManager->addErrorMessage($message);
        $this->response->setRedirect($request->getServer('HTTP_REFERER', ''));

        $this->response->sendResponse();
        exit();
    }

    /**
     * Can validate action
     *
     * @param Request $request
     * @param ActionInterface $action
     * @return bool
     */
    public function canValidate(Request $request, ActionInterface $action): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }
        if (!$request->isPost()) {
            return false;
        }

        foreach ($this->actions as $form => $instance) {
            if ($this->isFormEnabled($form) && $action instanceof $instance) {
                return true;
            }
        }

        return false;
    }

    /**
     * Persist data
     *
     * @param Request $request
     * @param ActionInterface $action
     * @return bool
     */
    public function persist(Request $request, ActionInterface $action): bool
    {
        return true;
    }

    /**
     * Test if the form is enabled
     *
     * @param string $form
     * @return bool
     */
    abstract public function isFormEnabled(string $form): bool;

    /**
     * Retrieve if validator is globally enabled
     *
     * @return bool
     */
    abstract public function isEnabled(): bool;
}
