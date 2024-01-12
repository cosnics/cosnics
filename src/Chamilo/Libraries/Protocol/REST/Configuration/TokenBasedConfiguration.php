<?php

namespace Chamilo\Libraries\Protocol\REST\Configuration;

/**
 * Class ProctorExamConfiguration
 * @package Hogent\Integration\ProctorExam\Domain
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class TokenBasedConfiguration extends RestConfiguration
{
    protected string $apiToken;

    public function __construct(string $apiURL, string $apiToken = null)
    {
        parent::__construct($apiURL);

        $this->apiToken = $apiToken;
    }

    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    public function isValidConfiguration()
    {
        return parent::isValidConfiguration() && !empty($this->apiToken);
    }
}
