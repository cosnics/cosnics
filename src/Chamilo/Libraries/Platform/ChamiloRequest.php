<?php
namespace Chamilo\Libraries\Platform;

use Symfony\Component\HttpFoundation\Request;

/**
 * @package Chamilo\Libraries\Platform
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class ChamiloRequest extends Request
{

    /**
     * Returns a parameter from the POST BODY and if it does not exist fallback on the URL QUERY.
     *
     * @deprecated Use ChamiloRequest::getFromQuery() or ChamiloRequest::getFromRequest() or
     *     ChamiloRequest::getFromRequestOrQuery()
     */
    public function get(string $key, $default = null)
    {
        return $this->getFromRequestOrQuery($key, $default);
    }

    public function getContainerMode(): string
    {
        return $this->attributes->get('containerMode', 'container-fluid');
    }

    /**
     * Returns a parameter from the url (query) or fallback to the default value
     */
    public function getFromQuery(string $key, $default = null)
    {
        if ($this !== $result = $this->query->get($key, $this))
        {
            return $result;
        }

        return $default;
    }

    /**
     * Returns a parameter from the post body (request) or fallback to the default value
     */
    public function getFromRequest(string $key, $default = null)
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
    public function getFromRequestOrQuery(string $key, $default = null)
    {
        if (null !== $result = $this->getFromRequest($key))
        {
            return $result;
        }

        if (null != $result = $this->getFromQuery($key))
        {
            return $result;
        }

        return $default;
    }

}