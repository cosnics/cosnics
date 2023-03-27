<?php
namespace Chamilo\Libraries\File;

use Chamilo\Libraries\Platform\ChamiloRequest;

/**
 * @package Chamilo\Libraries\File
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WebPathBuilder extends AbstractPathBuilder
{
    protected ChamiloRequest $request;

    public function getBasePath(): string
    {
        if (!isset($this->cache[self::BASE]))
        {
            $request = $this->getRequest();
            $this->cache[self::BASE] =
                $request->getSchemeAndHttpHost() . $request->getBasePath() . $request->getPathInfo();
        }

        return $this->cache[self::BASE];
    }

    public function getDirectorySeparator(): string
    {
        return '/';
    }

    protected function getPublicStorageBasePath(): string
    {
        return $this->getBasePath() . 'Files';
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    public function isWebUri(string $uri): bool
    {
        return ((stripos($uri, 'http://') === 0) || (stripos($uri, 'https://') === 0) ||
            (stripos($uri, 'ftp://') === 0));
    }
}
