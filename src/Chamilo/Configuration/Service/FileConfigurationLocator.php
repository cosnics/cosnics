<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Libraries\File\SystemPathBuilder;

/**
 * @package Chamilo\Configuration\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class FileConfigurationLocator
{

    private SystemPathBuilder $systemPathBuilder;

    public function __construct(SystemPathBuilder $pathBuilder)
    {
        $this->systemPathBuilder = $pathBuilder;
    }

    public function getDefaultFileName(): string
    {
        return 'configuration.default.xml';
    }

    public function getDefaultFilePath(): string
    {
        return $this->getSystemPathBuilder()->getConfigurationPath();
    }

    public function getDefaultFilePathName(): string
    {
        return $this->getDefaultFilePath() . DIRECTORY_SEPARATOR . $this->getDefaultFileName();
    }

    public function getFileName(): string
    {
        return 'configuration.xml';
    }

    public function getFilePath(): string
    {
        return $this->getSystemPathBuilder()->getStoragePath() . 'configuration';
    }

    public function getFilePathName(): string
    {
        return $this->getFilePath() . DIRECTORY_SEPARATOR . $this->getFileName();
    }

    public function getSystemPathBuilder(): SystemPathBuilder
    {
        return $this->systemPathBuilder;
    }

    public function isAvailable(): bool
    {
        $file = $this->getFilePathName();

        if (is_file($file) && is_readable($file))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
