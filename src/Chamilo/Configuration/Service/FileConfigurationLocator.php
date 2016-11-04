<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Libraries\File\PathBuilder;

/**
 *
 * @package Chamilo\Configuration\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class FileConfigurationLocator
{

    /**
     *
     * @var \Chamilo\Libraries\File\PathBuilder
     */
    private $pathBuilder;

    /**
     *
     * @param \Chamilo\Libraries\File\PathBuilder $pathBuilder
     */
    public function __construct(PathBuilder $pathBuilder)
    {
        $this->pathBuilder = $pathBuilder;
    }

    /**
     *
     * @return \Chamilo\Libraries\File\PathBuilder
     */
    public function getPathBuilder()
    {
        return $this->pathBuilder;
    }

    /**
     *
     * @param \Chamilo\Libraries\File\Path $pathBuilder
     */
    public function setPathBuilder(PathBuilder $pathBuilder)
    {
        $this->pathBuilder = $pathBuilder;
    }

    /**
     *
     * @throws \Exception
     * @return boolean
     */
    public function isAvailable()
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

    /**
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->getPathBuilder()->getStoragePath() . 'configuration';
    }

    /**
     *
     * @return string
     */
    public function getFileName()
    {
        return 'configuration.xml';
    }

    /**
     *
     * @return string
     */
    public function getFilePathName()
    {
        return $this->getFilePath() . DIRECTORY_SEPARATOR . $this->getFileName();
    }

    /**
     *
     * @return string
     */
    public function getDefaultFilePath()
    {
        return $this->getPathBuilder()->getConfigurationPath();
    }

    /**
     *
     * @return string
     */
    public function getDefaultFileName()
    {
        return 'configuration.default.xml';
    }

    /**
     *
     * @return string
     */
    public function getDefaultFilePathName()
    {
        return $this->getDefaultFilePath() . DIRECTORY_SEPARATOR . $this->getDefaultFileName();
    }
}
