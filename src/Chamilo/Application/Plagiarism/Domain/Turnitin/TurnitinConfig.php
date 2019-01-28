<?php

namespace Chamilo\Application\Plagiarism\Domain\Turnitin;

/**
 * @package Chamilo\Application\Plagiarism\Domain\Turnitin
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TurnitinConfig
{
    /**
     * @var string
     */
    protected $apiUrl;

    /**
     * @var string
     */
    protected $secretKey;

    /**
     * TurnitinConfig constructor.
     *
     * @param string $apiUrl
     * @param string $secretKey
     */
    public function __construct(string $apiUrl = '', string $secretKey = '')
    {
        $this->apiUrl = $apiUrl;
        $this->secretKey = $secretKey;
    }

    /**
     * @return string
     */
    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    /**
     * @return string
     */
    public function getSecretKey(): string
    {
        return $this->secretKey;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return !empty($this->apiUrl) && !empty($this->secretKey);
    }
}