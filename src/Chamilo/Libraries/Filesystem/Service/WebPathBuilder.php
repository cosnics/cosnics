<?php
namespace Chamilo\Libraries\Filesystem\Service;

use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;

/**
 * @package Chamilo\Libraries\Filesystem\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WebPathBuilder extends AbstractPathBuilder
{
    public function __construct(protected ChamiloRequest $request)
    {
    }

    public function getBasePath(): string
    {
        if (!isset($this->cache[self::BASE_PATH])) {
            $this->cache[self::BASE_PATH] =
                $this->request->getSchemeAndHttpHost() . $this->request->getBasePath() . $this->request->getPathInfo();
        }

        return $this->cache[self::BASE_PATH];
    }

    public function getDirectorySeparator(): string
    {
        return '/';
    }

    protected function getPublicStorageBasePath(): string
    {
        return $this->getBasePath() . 'Files';
    }

    public function isWebUri(string $uri): bool
    {
        return ((stripos($uri, 'https://') === 0) || (stripos($uri, 'ftp://') === 0));
    }
}
