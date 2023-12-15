<?php
/**
 * Copyright (C) 2023 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PixelOpen\CloudflareTurnstile\Model\Persistor;

use Magento\Contact\Controller\Index\Post as ContactPost;
use Magento\Customer\Controller\Account\CreatePost;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\Request\Http as Request;
use Magento\Framework\Session\Generic;
use Magento\Review\Controller\Product\Post as ReviewPost;
use PixelOpen\CloudflareTurnstile\Model\PersistorInterface;

class Frontend implements PersistorInterface
{
    protected DataPersistorInterface $dataPersistor;

    protected CustomerSession $customerSession;

    protected Generic $reviewSession;

    /**
     * @param DataPersistorInterface $dataPersistor
     * @param CustomerSession $customerSession
     * @param Generic $reviewSession
     */
    public function __construct(
        DataPersistorInterface $dataPersistor,
        CustomerSession $customerSession,
        Generic $reviewSession
    ) {
        $this->reviewSession = $reviewSession;
        $this->customerSession = $customerSession;
        $this->dataPersistor = $dataPersistor;
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
        if ($action instanceof CreatePost) {
            $this->customerSession->setCustomerFormData($request->getParams());
        }
        if ($action instanceof ReviewPost) {
            $this->reviewSession->setFormData($request->getParams());
        }
    }
}
