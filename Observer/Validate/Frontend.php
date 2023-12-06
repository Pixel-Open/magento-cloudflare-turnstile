<?php
/**
 * Copyright (C) 2023 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PixelOpen\CloudflareTurnstile\Observer\Validate;

use JetBrains\PhpStorm\NoReturn;
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
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Session\Generic;
use Magento\Review\Controller\Product\Post as ReviewPost;
use PixelOpen\CloudflareTurnstile\Helper\Config;
use PixelOpen\CloudflareTurnstile\Model\Config\Source\Forms\Frontend as FrontendForms;
use PixelOpen\CloudflareTurnstile\Model\Validator;
use PixelOpen\CloudflareTurnstile\Observer\Validate;

class Frontend extends Validate
{
    protected DataPersistorInterface $dataPersistor;

    protected CustomerSession $customerSession;

    protected Generic $reviewSession;

    /**
     * @param ManagerInterface $messageManager
     * @param Response $response
     * @param Validator $validator
     * @param Json $json
     * @param Config $config
     * @param DataPersistorInterface $dataPersistor
     * @param CustomerSession $customerSession
     * @param Generic $reviewSession
     */
    public function __construct(
        ManagerInterface $messageManager,
        Response $response,
        Validator $validator,
        Json $json,
        Config $config,
        DataPersistorInterface $dataPersistor,
        CustomerSession $customerSession,
        Generic $reviewSession
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->customerSession = $customerSession;
        $this->reviewSession = $reviewSession;

        parent::__construct($messageManager, $response, $validator, $json, $config);
    }

    /**
     * Can validate action
     *
     * @param Request         $request
     * @param ActionInterface $action
     * @return bool
     */
    public function canValidate(Request $request, ActionInterface $action): bool
    {
        if (!$this->config->isEnabled()) {
            return false;
        }
        if (!$request->isPost()) {
            return false;
        }
        if ($this->customerSession->isLoggedIn()) {
            return false;
        }
        if ($this->validator->isFrontendFormEnabled(FrontendForms::FORM_CONTACT) && $action instanceof ContactPost) {
            return true;
        }
        if ($this->validator->isFrontendFormEnabled(FrontendForms::FORM_PASSWORD) && $action instanceof ForgotPasswordPost) {
            return true;
        }
        if ($this->validator->isFrontendFormEnabled(FrontendForms::FORM_REGISTER) && $action instanceof CreatePost) {
            return true;
        }
        if ($this->validator->isFrontendFormEnabled(FrontendForms::FORM_LOGIN) && $action instanceof LoginPost) {
            return true;
        }
        if ($this->validator->isFrontendFormEnabled(FrontendForms::FORM_LOGIN_AJAX) && $action instanceof AjaxLoginPost) {
            return true;
        }
        if ($this->validator->isFrontendFormEnabled(FrontendForms::FORM_REVIEW) && $action instanceof ReviewPost) {
            return true;
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
        if ($action instanceof ContactPost) {
            $this->dataPersistor->set('contact_us', $request->getParams());
        }
        if ($action instanceof CreatePost) {
            $this->customerSession->setCustomerFormData($request->getParams());
        }
        if ($action instanceof ReviewPost) {
            $this->reviewSession->setFormData($request->getParams());
        }

        return true;
    }

    /**
     * Retrieve Cloudflare Turnstile response
     *
     * @param Request $request
     * @param ActionInterface $action
     * @return string|null
     */
    public function getCfResponse(Request $request, ActionInterface $action): ?string
    {
        if ($action instanceof AjaxLoginPost) {
            return $this->json->unserialize($request->getContent())['cf-turnstile-response'] ?? null;
        }
        return parent::getCfResponse($request, $action);
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
    #[NoReturn] protected function error(Request $request, ActionInterface $action, Phrase $message): void
    {
        if ($action instanceof AjaxLoginPost) {
            $data = [
                'errors'  => true,
                'message' => $message
            ];
            $this->response->representJson($this->json->serialize($data));

            $this->response->sendResponse();
            exit();
        }

        parent::error($request, $action, $message);
    }
}
