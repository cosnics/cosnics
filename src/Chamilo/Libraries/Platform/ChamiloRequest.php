<?php
namespace Chamilo\Libraries\Platform;

use Symfony\Component\HttpFoundation\Request;

/**
 * @package Chamilo\Libraries\Platform
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class ChamiloRequest extends Request
{

    public function getArrayFromQueryOrRequest(string $key, $default = []): array
    {
        if ($this->query->has($key))
        {
            return $this->query->all($key);
        }

        if ($this->request->has($key))
        {
            return $this->request->all($key);
        }

        return $default;
    }

    public function getArrayFromRequestOrQuery(string $key, $default = []): array
    {
        if ($this->request->has($key))
        {
            return $this->request->all($key);
        }

        if ($this->query->has($key))
        {
            return $this->query->all($key);
        }

        return $default;
    }

    public function getContainerMode(): string
    {
        return $this->attributes->get('containerMode', 'container-fluid');
    }

    public function getFromQueryOrRequest(string $key, $default = null): string|int|float|bool|null
    {
        if ($this->query->has($key))
        {
            return $this->query->get($key);
        }

        if ($this->request->has($key))
        {
            return $this->request->get($key);
        }

        return $default;
    }

    /**
     * Returns a parameter from the POST BODY and if it does not exist fallback on the URL QUERY.
     */
    public function getFromRequestOrQuery(string $key, $default = null): string|int|float|bool|null
    {
        if ($this->request->has($key))
        {
            return $this->request->get($key);
        }

        if ($this->query->has($key))
        {
            return $this->query->get($key);
        }

        return $default;
    }

    public function hasRequestOrQuery(string $key): bool
    {
        return $this->request->has($key) || $this->query->has($key);
    }

}