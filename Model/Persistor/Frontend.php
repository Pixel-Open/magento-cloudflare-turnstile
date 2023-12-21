<?php
/**
 * Copyright (C) 2023 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PixelOpen\CloudflareTurnstile\Model\Persistor;

use Magento\Catalog\Model\Session;
use Magento\Contact\Controller\Index\Post as ContactPost;
use Magento\Customer\Controller\Account\CreatePost as CreateAccountPost;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\Request\Http as Request;
use Magento\Framework\Session\Generic;
use Magento\Review\Controller\Product\Post as ProductReviewPost;
use Magento\SendFriend\Controller\Product\Sendmail as SendFriendPost;
use PixelOpen\CloudflareTurnstile\Model\PersistorInterface;

class Frontend implements PersistorInterface
{
    protected DataPersistorInterface $dataPersistor;

    protected CustomerSession $customerSession;

    protected Generic $reviewSession;

    protected Session $catalogSession;

    /**
     * @param DataPersistorInterface $dataPersistor
     * @param CustomerSession $customerSession
     * @param Generic $reviewSession
     * @param Session $catalogSession
     */
    public function __construct(
        DataPersistorInterface $dataPersistor,
        CustomerSession $customerSession,
        Generic $reviewSession,
        Session $catalogSession
    ) {
        $this->reviewSession = $reviewSession;
        $this->customerSession = $customerSession;
        $this->dataPersistor = $dataPersistor;
        $this->catalogSession = $catalogSession;
    }

    /**
     * Persist data
     *
     * @param Request $request
     * @param ActionInterface $action
     * @return void
     */
    public function persist(Request $request, ActionInterface $action): void
    {
        if ($action instanceof ContactPost) {
            $this->dataPersistor->set('contact_us', $request->getParams());
        }
        if ($action instanceof CreateAccountPost) {
            $this->customerSession->setCustomerFormData($request->getParams());
        }
        if ($action instanceof ProductReviewPost) {
            $this->reviewSession->setFormData($request->getParams());
        }
        if ($action instanceof SendFriendPost) {
            $this->catalogSession->setSendfriendFormData($request->getPostValue());
        }
    }
}
