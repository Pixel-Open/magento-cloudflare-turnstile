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
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\Http as Request;
use Magento\Framework\App\Response\Http as Response;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Serialize\Serializer\Json;
use PixelOpen\CloudflareTurnstile\Helper\Config;
use PixelOpen\CloudflareTurnstile\Model\PersistorInterface;
use PixelOpen\CloudflareTurnstile\Model\Validator;

abstract class Validate implements ObserverInterface
{
    protected ManagerInterface $messageManager;

    protected Response $response;

    protected RedirectInterface $redirect;

    protected Validator $validator;

    protected Json $json;

    protected Config $config;

    protected ?PersistorInterface $persistor = null;

    protected array $actions = [];

    public ?ActionInterface $action = null;

    public ?Request $request = null;

    /**
     * @param ManagerInterface $messageManager
     * @param Validator $validator
     * @param Json $json
     * @param Config $config
     * @param PersistorInterface|null $persistor
     * @param array $data
     */
    public function __construct(
        ManagerInterface $messageManager,
        RedirectInterface $redirect,
        Validator $validator,
        Json $json,
        Config $config,
        ?PersistorInterface $persistor = null,
        array $data = []
    ) {
        $this->messageManager = $messageManager;
        $this->redirect       = $redirect;
        $this->validator      = $validator;
        $this->json           = $json;
        $this->config         = $config;
        $this->persistor      = $persistor;
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
        $this->action   = $observer->getEvent()->getData('controller_action');
        $this->request  = $observer->getEvent()->getData('request');
        $this->response = $this->action->getResponse();

        if ($this->canValidate()) {
            try {
                if (!$this->validator->isValid($this->getCfResponse())) {
                    $this->persistor?->persist($this->request, $this->action);
                    $this->error(
                        __('Security validation error: %1', join(', ', $this->validator->getErrorMessages()))
                    );
                }
            } catch (Exception $exception) {
                $this->error(__('Security validation error: %1', $exception->getMessage()));
            }
        }
    }

    /**
     * Retrieve Cloudflare Turnstile response
     *
     * @return string|null
     */
    public function getCfResponse(): ?string
    {
        return $this->request?->getParam('cf-turnstile-response');
    }

    /**
     * Send error
     *
     * @param Phrase $message
     * @return void
     */
    protected function error(Phrase $message): void
    {
        $this->messageManager->addErrorMessage($message);
        $this->redirect->redirect($this->response, $this->redirect->getRefererUrl());

        exit();
    }

    /**
     * Can validate action
     *
     * @return bool
     */
    public function canValidate(): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }
        if (!$this->request?->isPost()) {
            return false;
        }

        foreach ($this->actions as $form => $instance) {
            if ($this->isFormEnabled($form) && is_a($this->action, $instance)) {
                return true;
            }
        }

        return false;
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
