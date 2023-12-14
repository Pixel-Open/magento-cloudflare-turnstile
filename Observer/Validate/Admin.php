<?php
/**
 * Copyright (C) 2023 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PixelOpen\CloudflareTurnstile\Observer\Validate;

use PixelOpen\CloudflareTurnstile\Observer\Validate;

class Admin extends Validate
{
    /**
     * Retrieve if validator is globally enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->config->isEnabledOnAdmin();
    }

    /**
     * Test if the form is enabled
     *
     * @param string $form
     * @return bool
     */
    public function isFormEnabled(string $form): bool
    {
        return in_array($form, $this->config->getAdminForms());
    }
}
