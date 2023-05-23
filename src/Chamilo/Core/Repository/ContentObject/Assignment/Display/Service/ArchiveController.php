<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Service;

use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\Compression\ZipArchive\ZipArchiveFilecompression;
use Chamilo\Libraries\File\Filesystem;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ArchiveController
{

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var string
     */
    private $temporaryPath;

    /**
     * @param string $temporaryPath
     * @param string $fileName
     */
    public function __construct($temporaryPath, $fileName)
    {
        $this->prepareFileSystem($temporaryPath);
        $this->fileName = $fileName;
    }

    /**
     * @param string $source
     * @param string $destination
     */
    public function addPath($sourcePath, $targetPath)
    {
        return Filesystem::recurse_copy($sourcePath, $this->temporaryPath . $targetPath, true);
    }

    /**
     * @return string
     * @deprecated
     */
    public function getArchivePath()
    {
        $archivePath =
            $this->getZipArchiveFilecompression()->createArchive($this->temporaryPath, $this->fileName, 'zip');
        Filesystem::remove($this->temporaryPath);

        return $archivePath;
    }

    protected function getZipArchiveFilecompression(): ZipArchiveFilecompression
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            ZipArchiveFilecompression::class
        );
    }

    /**
     * @param string $temporaryPath
     */
    protected function prepareFileSystem($temporaryPath)
    {
        $this->temporaryPath = $temporaryPath;

        if (!is_dir($this->temporaryPath))
        {
            mkdir($this->temporaryPath, 0777, true);
        }
    }
}
