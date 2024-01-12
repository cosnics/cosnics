<?php

namespace Chamilo\Libraries\Protocol\REST\Configuration;

/**
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class RestConfiguration
{
    /**
     * @var string
     */
    protected $apiURL;

    /**
     * APIConfiguration constructor.
     *
     * @param string $apiURL
     */
    public function __construct(string $apiURL)
    {
        $this->apiURL = $apiURL;
    }

    /**
     * @return string
     */
    public function getApiURL(): ?string
    {
        return $this->apiURL;
    }

    /**
     * @return bool
     */
    public function isValidConfiguration()
    {
        return !empty($this->apiURL);
    }
}
