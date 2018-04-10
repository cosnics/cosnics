<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Service;

use Chamilo\Libraries\File\Compression\Filecompression;
use Chamilo\Libraries\File\Filesystem;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ArchiveController
{

    /**
     *
     * @var string
     */
    private $temporaryPath;

    /**
     *
     * @var string
     */
    private $fileName;

    /**
     *
     * @param string $temporaryPath
     * @param string $fileName
     */
    public function __construct($temporaryPath, $fileName)
    {
        $this->prepareFileSystem($temporaryPath);
        $this->fileName = $fileName;
    }

    /**
     *
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

    /**
     *
     * @param string $source
     * @param string $destination
     */
    public function addPath($sourcePath, $targetPath)
    {
        return Filesystem::recurse_copy($sourcePath, $this->temporaryPath . $targetPath, true);
    }

    /**
     * @deprecated
     * @return string
     */
    public function getArchivePath()
    {
        $fileCompressor = Filecompression::factory();
        $fileCompressor->set_filename($this->fileName, 'zip');
        $archivePath = $fileCompressor->create_archive($this->temporaryPath);
        Filesystem::remove($this->temporaryPath);

        return $archivePath;
    }
}
