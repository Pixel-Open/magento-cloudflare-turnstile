<?php
/**
 * Copyright (C) 2023 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PixelOpen\CloudflareTurnstile\Observer\Validate;

use Magento\Backend\Controller\Adminhtml\Index\Index as Login;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\Http as Request;
use Magento\User\Controller\Adminhtml\Auth\Forgotpassword;
use PixelOpen\CloudflareTurnstile\Model\Config\Source\Forms\Adminhtml as AdminForms;
use PixelOpen\CloudflareTurnstile\Observer\Validate;

class Admin extends Validate
{
    /**
     * Can validate action
     *
     * @param Request         $request
     * @param ActionInterface $action
     * @return bool
     */
    public function canValidate(Request $request, ActionInterface $action): bool
    {
        if (!$this->config->isEnabledOnAdmin()) {
            return false;
        }
        if (!$request->isPost()) {
            return false;
        }
        if ($this->validator->isAdminFormEnabled(AdminForms::FORM_LOGIN) && $action instanceof Login) {
            return true;
        }
        if ($this->validator->isAdminFormEnabled(AdminForms::FORM_PASSWORD) && $action instanceof Forgotpassword) {
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
        return true;
    }
}
