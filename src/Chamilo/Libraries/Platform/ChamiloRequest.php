<?php

namespace Chamilo\Libraries\Platform;

use Symfony\Component\HttpFoundation\Request;

/**
 * @package Chamilo\Libraries\Platform
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ChamiloRequest extends Request
{
    /**
     * Returns a parameter from the POST BODY and if it does not exist fallback on the URL QUERY.
     *
     * @param string $key
     * @param mixed $default
     * @param bool $deep
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
            return $result;
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
            return $result;
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
        if(null !== $result = $this->getFromPOST($key))
        {
            return $result;
        }

        if(null != $result = $this->getFromURL($key))
        {
            return $result;
        }

        return $default;
    }
}