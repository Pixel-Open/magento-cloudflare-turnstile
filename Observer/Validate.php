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
use Magento\Customer\Controller\Ajax\Login as AjaxLoginPost;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\Request\Http as Request;
use Magento\Framework\App\Response\Http as Response;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Session\Generic;
use Magento\Review\Controller\Product\Post as ReviewPost;
use PixelOpen\CloudflareTurnstile\Model\Config\Source\Forms;
use PixelOpen\CloudflareTurnstile\Model\Validator;

class Validate implements ObserverInterface
{
    protected ManagerInterface $messageManager;

    protected Response $response;

    protected DataPersistorInterface $dataPersistor;

    protected CustomerSession $customerSession;

    protected Generic $reviewSession;

    protected Validator $validator;

    protected Json $json;

    /**
     * @param ManagerInterface       $messageManager
     * @param Response               $response
     * @param DataPersistorInterface $dataPersistor
     * @param CustomerSession        $customerSession
     * @param Generic                $reviewSession
     * @param Validator              $validator
     * @param Json                   $json
     */
    public function __construct(
        ManagerInterface $messageManager,
        Response $response,
        DataPersistorInterface $dataPersistor,
        CustomerSession $customerSession,
        Generic $reviewSession,
        Validator $validator,
        Json $json
    ) {
        $this->messageManager  = $messageManager;
        $this->response        = $response;
        $this->dataPersistor   = $dataPersistor;
        $this->customerSession = $customerSession;
        $this->reviewSession   = $reviewSession;
        $this->validator       = $validator;
        $this->json            = $json;
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
        if ($action instanceof AjaxLoginPost) {
            return $this->json->unserialize($request->getContent())['cf-turnstile-response'] ?? null;
        }
        return $request->getParam('cf-turnstile-response');
    }

    /**
     * Can validate action
     *
     * @param Request         $request
     * @param ActionInterface $action
     *
     * @return bool
     */
    public function canValidate(Request $request, ActionInterface $action): bool
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
        if ($this->validator->isFormEnabled(forms::FORM_LOGIN_AJAX) && $action instanceof AjaxLoginPost) {
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
     * @param Request $request
     * @param ActionInterface $action
     *
     * @return void
     */
    public function persist(Request $request, ActionInterface $action): void
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
     * @param Request $request
     * @param ActionInterface $action
     * @param Phrase $message
     *
     * @return void
     */
    protected function error(Request $request, ActionInterface $action, Phrase $message): void
    {
        if ($action instanceof AjaxLoginPost) {
            $data = [
                'errors'  => true,
                'message' => $message
            ];
            $this->response->representJson($this->json->serialize($data));
        } else {
            $this->messageManager->addErrorMessage($message);
            $this->response->setRedirect($request->getServer('HTTP_REFERER', ''));
        }

        $this->response->sendResponse();
        exit();
    }
}
