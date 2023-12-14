<?php
/**
 * Copyright (C) 2023 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PixelOpen\CloudflareTurnstile\Model;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\Http as Request;

interface PersistorInterface
{
    /**
     * Persist Data
     *
     * @param Request $request
     * @param ActionInterface $action
     * @return void
     */
    public function persist(Request $request, ActionInterface $action): void;
}
