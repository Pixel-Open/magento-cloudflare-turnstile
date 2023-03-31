<?php
/**
 * Copyright (C) 2023 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PixelOpen\CloudflareTurnstile\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Forms implements OptionSourceInterface
{
    public const FORM_CONTACT = 'contact';
    public const FORM_REGISTER = 'register';
    public const FORM_LOGIN = 'login';
    public const FORM_LOGIN_AJAX = 'login-ajax';
    public const FORM_PASSWORD = 'password';
    public const FORM_REVIEW = 'review';

    /**
     * Get options as array
     *
     * @return string[][]
     */
    public function toOptionArray(): array
    {
        $options = [];

        foreach ($this->toArray() as $value) {
            $options[] = [
                'value' => $value,
                'label' => $value,
            ];
        }

        return $options;
    }

    /**
     * Get options as array
     *
     * @return string[]
     */
    public function toArray(): array
    {
        return [
            self::FORM_CONTACT,
            self::FORM_REGISTER,
            self::FORM_LOGIN,
            self::FORM_LOGIN_AJAX,
            self::FORM_PASSWORD,
            self::FORM_REVIEW,
        ];
    }
}
