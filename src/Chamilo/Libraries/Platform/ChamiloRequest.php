<?php

namespace Chamilo\Libraries\Platform;

use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @package Chamilo\Libraries\Platform
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ChamiloRequest extends Request
{
    /**
     * @var Security
     */
    protected $security;

    public function __construct(
        array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [],
        array $server = [], $content = null
    )
    {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
        $this->security = new Security();
    }

    /**
     * Use for testing purposes
     *
     * @param string $content
     */
    public function setContent(string $content)
    {
        $this->content = $content;
    }

    /**
     * Returns a parameter from the POST BODY and if it does not exist fallback on the URL QUERY.
     *
     * @param string $key
     * @param mixed $default
     * @param boolean $deep
     *
     * @return mixed
     * @deprecated
     *
     * @see getFromUrl
     * @see getFromPost
     * @see getFromPostOrUrl
     */
    public function get($key, $default = null, $deep = false)
    {
        return $this->getFromPOSTOrURL($key, $default);
    }

    /**
     * Returns a parameter from the url (query) or fallback to the default value
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getFromUrl($key, $default = null)
    {
        if ($this !== $result = $this->query->get($key, $this))
        {
            return $this->security->removeXSS($result);
        }

        return $default;
    }

    /**
     * Returns a parameter from the post body (request) or fallback to the default value
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getFromPost($key, $default = null)
    {
        if ($this !== $result = $this->request->get($key, $this))
        {
            return $this->security->removeXSS($result);
        }

        return $default;
    }

    /**
     * Returns a parameter from the POST BODY and if it does not exist fallback on the URL QUERY.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getFromPostOrUrl($key, $default = null)
    {
        if (null !== $result = $this->getFromPOST($key))
        {
            return $this->security->removeXSS($result);
        }

        if (null != $result = $this->getFromURL($key))
        {
            return $this->security->removeXSS($result);
        }

        return $default;
    }
}
