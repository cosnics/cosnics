<?php
namespace Chamilo\Libraries\Filesystem\Service;

/**
 * @package Chamilo\Libraries\Filesystem\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SystemPathBuilder extends AbstractPathBuilder
{
    public function getBasePath(): string
    {
        if (!isset($this->cache[self::BASE_PATH])) {
            $directorySeparator = $this->getDirectorySeparator();

            $this->cache[self::BASE_PATH] = realpath(
                    __DIR__ . $directorySeparator . '..' . $directorySeparator . '..' . $directorySeparator . '..' .
                    $directorySeparator . '..' . $directorySeparator
                ) . $directorySeparator;
        }

        return $this->cache[self::BASE_PATH];
    }

    public function getConfigurationStoragePath(): string
    {
        return $this->cache[self::CONFIGURATION_STORAGE_PATH] =
            $this->getStoragePath() . 'configuration' . $this->getDirectorySeparator();
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

        return $this->cache[self::ROOT_PATH] =
            realpath($this->getBasePath() . '..' . $directorySeparator) . $directorySeparator;
    }

    public function getStoragePath(?string $namespace = null): string
    {
        $directorySeparator = $this->getDirectorySeparator();

        $basePath = $this->getRootPath() . $directorySeparator . 'files';

        return $this->cache[self::STORAGE_PATH][(string) $namespace] = $basePath . $directorySeparator .
            ($namespace ? $this->namespaceToPath($namespace) . $directorySeparator : '');
    }

    public function getVendorPath(): string
    {
        $directorySeparator = $this->getDirectorySeparator();

        return $this->cache[self::VENDOR_PATH] = $this->getRootPath() . $directorySeparator . 'vendor' . $directorySeparator;
    }
}
