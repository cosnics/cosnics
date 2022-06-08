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
     * Returns a parameter from the POST BODY and if it does not exist fallback on the URL QUERY.
     *
     * @deprecated Use ChamiloRequest::getFromUrl() or ChamiloRequest::getFromPost() or
     *     ChamiloRequest::getFromPostOrUrl()
     */
    public function get(string $key, $default = null)
    {
        return $this->getFromPOSTOrURL($key, $default);
    }

    /**
     * Returns a parameter from the post body (request) or fallback to the default value
     */
    public function getFromPost(string $key, $default = null)
    {
        if ($this !== $result = $this->request->get($key, $this))
        {
            return $result;
        }

        return $default;
    }

    /**
     * Returns a parameter from the POST BODY and if it does not exist fallback on the URL QUERY.
     */
    public function getFromPostOrUrl(string $key, $default = null)
    {
        if (null !== $result = $this->getFromPost($key))
        {
            return $result;
        }

        if (null != $result = $this->getFromURL($key))
        {
            return $result;
        }

        return $default;
    }

    /**
     * Returns a parameter from the url (query) or fallback to the default value
     */
    public function getFromUrl(string $key, $default = null)
    {
        if ($this !== $result = $this->query->get($key, $this))
        {
            return $result;
        }

        return $default;
    }
}