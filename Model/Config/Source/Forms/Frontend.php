<?php
/**
 * Copyright (C) 2025 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PixelOpen\CloudflareTurnstile\Model\Config\Source\Forms;

use PixelOpen\CloudflareTurnstile\Model\Config\Source\Forms;

class Frontend extends Forms
{
    public const FORM_CONTACT = 'contact';
    public const FORM_REGISTER = 'register';
    public const FORM_LOGIN = 'login';
    public const FORM_LOGIN_AJAX = 'login-ajax';
    public const FORM_PASSWORD = 'password';
    public const FORM_REVIEW = 'review';
    public const FORM_SEND_FRIEND = 'send-friend';
    public const FORM_NEWSLETTER = 'newsletter';

    public function toArray(): array
    {
        return [
            self::FORM_CONTACT,
            self::FORM_REGISTER,
            self::FORM_LOGIN,
            self::FORM_LOGIN_AJAX,
            self::FORM_PASSWORD,
            self::FORM_REVIEW,
            self::FORM_SEND_FRIEND,
            self::FORM_NEWSLETTER,
        ];
    }
}
