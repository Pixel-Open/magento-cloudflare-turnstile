<?php
/**
 * Copyright (C) 2022 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PixelOpen\CloudflareTurnstile\Observer;

use Exception;
use Magento\Contact\Controller\Index\Post as ContactPost;
use Magento\Customer\Controller\Account\CreatePost;
use Magento\Customer\Controller\Account\ForgotPasswordPost;
use Magento\Customer\Controller\Account\LoginPost;
use Magento\Customer\Controller\Ajax\Login as AjaxLogin;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Session\Generic;
use Magento\Review\Controller\Product\Post as ReviewPost;
use  PixelOpen\CloudflareTurnstile\Model\Config\Source\Forms;
use PixelOpen\CloudflareTurnstile\Model\Validator;

class Validate implements ObserverInterface
{
    protected ManagerInterface $messageManager;

    protected RedirectInterface $redirect;

    protected DataPersistorInterface $dataPersistor;

    protected CustomerSession $customerSession;

    protected Generic $reviewSession;

    protected Validator $validator;

    protected Json $json;

    protected ActionFlag $actionFlag;

    /**
     * @param ManagerInterface       $messageManager
     * @param RedirectInterface      $redirect
     * @param DataPersistorInterface $dataPersistor
     * @param CustomerSession        $customerSession
     * @param Generic                $reviewSession
     * @param Validator              $validator
     * @param Json                   $json
     * @param ActionFlag             $actionFlag
     */
    public function __construct(
        ManagerInterface $messageManager,
        RedirectInterface $redirect,
        DataPersistorInterface $dataPersistor,
        CustomerSession $customerSession,
        Generic $reviewSession,
        Validator $validator,
        Json $json,
        ActionFlag $actionFlag
    ) {
        $this->messageManager  = $messageManager;
        $this->redirect        = $redirect;
        $this->dataPersistor   = $dataPersistor;
        $this->customerSession = $customerSession;
        $this->reviewSession   = $reviewSession;
        $this->validator       = $validator;
        $this->json            = $json;
        $this->actionFlag      = $actionFlag;
    }

    /**
     * Validate Cloudflare Turnstile
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer): void
    {
        /** @var ActionInterface $action */
        $action = $observer->getEvent()->getData('controller_action');
        /** @var Http $request */
        $request = $observer->getEvent()->getData('request');

        if ($this->canValidate($request, $action)) {
            try {
                if (!$this->validator->isValid($this->getCfResponse($request, $action))) {
                    $this->persist($request, $action);
                    $this->error(
                        $action,
                        __('Security validation error: %1', join(', ', $this->validator->getErrorMessages()))
                    );
                }
            } catch (Exception $exception) {
                $this->error($action, __('Security validation error: %1', $exception->getMessage()));
            }
        }
    }

    /**
     * Retrieve Cloudflare Turnstile response
     *
     * @param Http $request
     * @param ActionInterface $action
     *
     * @return string|null
     */
    public function getCfResponse(Http $request, ActionInterface $action): ?string
    {
        if ($action instanceof AjaxLogin) {
            return $this->json->unserialize($request->getContent())['cf-turnstile-response'] ?? null;
        }
        return $request->getParam('cf-turnstile-response');
    }

    /**
     * Can validate action
     *
     * @param Http            $request
     * @param ActionInterface $action
     *
     * @return bool
     */
    public function canValidate(Http $request, ActionInterface $action): bool
    {
        if (!$request->isPost()) {
            return false;
        }
        if ($this->customerSession->isLoggedIn()) {
            return false;
        }
        if ($this->validator->isFormEnabled(forms::FORM_CONTACT) && $action instanceof ContactPost) {
            return true;
        }
        if ($this->validator->isFormEnabled(forms::FORM_PASSWORD) && $action instanceof ForgotPasswordPost) {
            return true;
        }
        if ($this->validator->isFormEnabled(forms::FORM_REGISTER) && $action instanceof CreatePost) {
            return true;
        }
        if ($this->validator->isFormEnabled(forms::FORM_LOGIN) && $action instanceof LoginPost) {
            return true;
        }
        if ($this->validator->isFormEnabled(forms::FORM_LOGIN_AJAX) && $action instanceof AjaxLogin) {
            return true;
        }
        if ($this->validator->isFormEnabled(forms::FORM_REVIEW) && $action instanceof ReviewPost) {
            return true;
        }

        return false;
    }

    /**
     * Persist data
     *
     * @param Http $request
     * @param ActionInterface $action
     *
     * @return void
     */
    public function persist(Http $request, ActionInterface $action): void
    {
        if ($action instanceof ContactPost) {
            $this->dataPersistor->set('contact_us', $request->getParams());
        }
        if ($action instanceof CreatePost) {
            $this->customerSession->setCustomerFormData($request->getParams());
        }
        if ($action instanceof ReviewPost) {
            $this->reviewSession->setFormData($request->getParams());
        }
    }

    /**
     * Send error
     *
     * @param ActionInterface $action
     * @param Phrase $message
     *
     * @return void
     */
    protected function error(ActionInterface $action, Phrase $message): void
    {
        $this->actionFlag->set('', ActionInterface::FLAG_NO_DISPATCH, true);
        $this->actionFlag->set('', ActionInterface::FLAG_NO_POST_DISPATCH, true);
        if ($action instanceof AjaxLogin) {
            $data = [
                'errors'  => true,
                'message' => $message
            ];
            $action->getResponse()->representJson($this->json->serialize($data));
        } else {
            $this->messageManager->addErrorMessage($message);
            $this->redirect->redirect($action->getResponse(), $this->redirect->getRefererUrl());
        }
    }
}
