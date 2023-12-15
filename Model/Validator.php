<?php
/**
 * Copyright (C) 2023 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PixelOpen\CloudflareTurnstile\Model;

use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;
use PixelOpen\CloudflareTurnstile\Helper\Config;

class Validator
{
    protected Curl $curl;

    protected Json $json;

    protected Config $config;

    protected array $errors;

    /**
     * @param Curl $curl
     * @param Json $json
     * @param Config $config
     */
    public function __construct(
        Curl $curl,
        Json $json,
        Config $config
    ) {
        $this->curl = $curl;
        $this->json = $json;
        $this->config = $config;
    }

    /**
     * Test if response is valid
     *
     * @param string|null $response
     * @return bool
     */
    public function isValid(?string $response): bool
    {
        if (!$response) {
            $this->errors = ['x-missing-response'];
            return false;
        }
        if (!$this->getSecretKey()) {
            $this->errors = ['x-missing-secret-key'];
            return false;
        }

        $data = [
            'secret'   => $this->getSecretKey(),
            'response' => $response,
        ];

        $curl = $this->curl;
        $curl->setTimeout(3);
        $curl->post($this->getApiUrl(), $data);
        $result = $this->json->unserialize($curl->getBody());

        if (!($result['success'] ?? false)) {
            $this->errors = $result['error-codes'] ?? ['x-unavailable'];
            return false;
        }

        return true;
    }

    /**
     * Retrieve error message
     *
     * @return string[]
     */
    public function getErrorMessages(): array
    {
        $messages = [];

        foreach ($this->getErrorCodes() as $code) {
            $messages[] = __($this->getErrorMessage($code));
        }

        return $messages;
    }

    /**
     * Retrieve all error codes
     *
     * @return string[]
     */
    public function getErrorCodes(): array
    {
        return $this->errors;
    }

    /**
     * Retrieve error message from error code
     *
     * @param string $code
     * @return string
     */
    protected function getErrorMessage(string $code): string
    {
        $messages = [
            'x-missing-response'     => 'please validate the security field.',
            'x-missing-secret-key'   => 'unable to validate the form, the secret key is missing.',
            'x-unavailable'          => 'unable to contact Cloudflare to validate the form.',
            'missing-input-secret'   => 'the secret parameter was not passed.',
            'invalid-input-secret'   => 'the secret parameter was invalid or did not exist.',
            'missing-input-response' => 'the response parameter was not passed.',
            'invalid-input-response' => 'the response parameter is invalid or has expired.',
            'bad-request'            => 'the request was rejected because it was malformed.',
            'timeout-or-duplicate'   => 'the response parameter has already been validated before.',
            'internal-error'         => 'an internal error happened while validating the response.',
        ];

        return $messages[$code] ?? 'unknown error.';
    }

    /**
     * Retrieve Secret Key
     *
     * @return string|null
     */
    protected function getSecretKey(): ?string
    {
        return $this->config->getSecretKey();
    }

    /**
     * Retrieve Secret Key
     *
     * @return string
     */
    protected function getApiUrl(): string
    {
        return $this->config->getApiUrl();
    }
}
