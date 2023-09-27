<?php
namespace Chamilo\Libraries\File;

/**
 * @package Chamilo\Libraries\File
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SystemPathBuilder extends AbstractPathBuilder
{

    public function getBasePath(): string
    {
        if (!isset($this->cache[self::BASE]))
        {
            $directorySeparator = $this->getDirectorySeparator();

            $this->cache[self::BASE] = realpath(
                    __DIR__ . $directorySeparator . '..' . $directorySeparator . '..' . $directorySeparator . '..' .
                    $directorySeparator
                ) . $directorySeparator;
        }

        return $this->cache[self::BASE];
    }

    public function getDirectorySeparator(): string
    {
        return DIRECTORY_SEPARATOR;
    }

    public function getPublicPath(): string
    {
        $directorySeparator = $this->getDirectorySeparator();

        return $this->getRootPath() . $directorySeparator . 'web' . $directorySeparator;
    }

    protected function getPublicStorageBasePath(): string
    {
        return $this->getPublicPath() . 'Files' . $this->getDirectorySeparator();
    }

    public function getRootPath(): string
    {
        $directorySeparator = $this->getDirectorySeparator();

        return $this->cache[self::ROOT] =
            realpath($this->getBasePath() . '..' . $directorySeparator) . $directorySeparator;
    }

    public function getStoragePath(?string $namespace = null): string
    {
        $directorySeparator = $this->getDirectorySeparator();

        $basePath = $this->getRootPath() . $directorySeparator . 'files';

        return $this->cache[self::STORAGE][(string) $namespace] = $basePath . $directorySeparator .
            ($namespace ? $this->namespaceToPath($namespace) . $directorySeparator : '');
    }

    public function getVendorPath(): string
    {
        $directorySeparator = $this->getDirectorySeparator();

        return $this->cache[self::VENDOR] = $this->getRootPath() . $directorySeparator . 'vendor' . $directorySeparator;
    }
}
