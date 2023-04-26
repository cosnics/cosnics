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
            $this->cache[self::BASE] = realpath(__DIR__ . '/../../../') . DIRECTORY_SEPARATOR;
        }

        return $this->cache[self::BASE];
    }

    public function getDirectorySeparator(): string
    {
        return DIRECTORY_SEPARATOR;
    }

    protected function getPublicStorageBasePath(): string
    {
        return realpath(
            $this->getBasePath() . '..' . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'Files'
        );
    }

    public function getStoragePath(?string $namespace = null): string
    {
        $basePath = realpath($this->getBasePath() . '../files/');

        return $this->cache[self::STORAGE][(string) $namespace] = $basePath . DIRECTORY_SEPARATOR .
            ($namespace ? $this->getClassnameUtilities()->namespaceToPath($namespace) . DIRECTORY_SEPARATOR : '');
    }

    public function getVendorPath(): string
    {
        return $this->cache[self::VENDOR] =
            realpath($this->getBasePath() . '../vendor/') . $this->getDirectorySeparator();
    }
}
