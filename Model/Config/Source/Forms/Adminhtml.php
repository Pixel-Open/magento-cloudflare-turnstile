<?php
/**
 * Copyright (C) 2023 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PixelOpen\CloudflareTurnstile\Model\Config\Source\Forms;

use PixelOpen\CloudflareTurnstile\Model\Config\Source\Forms;

class Adminhtml extends Forms
{
    public const FORM_LOGIN = 'login';
    public const FORM_PASSWORD = 'password';

    public function toArray(): array
    {
        return [
            self::FORM_LOGIN,
            self::FORM_PASSWORD,
        ];
    }
}
