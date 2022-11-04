<?php
/**
 * Copyright (C) 2022 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PixelOpen\CloudflareTurnstile\Block\Turnstile;

use PixelOpen\CloudflareTurnstile\Model\Turnstile\ConfigProvider;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Config extends Template
{
    protected SerializerInterface $serializer;

    protected ConfigProvider $configProvider;

    /**
     * @param Context $context
     * @param SerializerInterface $serializer
     * @param ConfigProvider $configProvider
     * @param mixed[] $data
     */
    public function __construct(
        Context $context,
        SerializerInterface $serializer,
        ConfigProvider $configProvider,
        array $data = []
    ) {
        $this->serializer     = $serializer;
        $this->configProvider = $configProvider;

        parent::__construct($context, $data);
    }

    /**
     * Retrieve configuration
     *
     * @return string[]
     */
    public function getTurnstileConfig(): array
    {
        return $this->configProvider->getConfig();
    }

    /**
     * Retrieve serialized config
     *
     * @return string
     */
    public function getSerializedTurnstileConfig(): string
    {
        return (string)$this->serializer->serialize($this->getTurnstileConfig());
    }
}
