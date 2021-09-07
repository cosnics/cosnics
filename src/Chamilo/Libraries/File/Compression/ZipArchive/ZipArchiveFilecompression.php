<?php
namespace Chamilo\Libraries\File\Compression\ZipArchive;

use Chamilo\Libraries\File\Compression\Filecompression;
use Chamilo\Libraries\File\Filesystem;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

/**
 * @package Chamilo\Libraries\File\Compression\ZipArchive
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ZipArchiveFilecompression extends Filecompression
{
    /**
     * @param string $path
     *
     * @return string
     */
    public function create_archive($path)
    {
        $pathToBeZipped = realpath($path);
        $archiveFileName = $this->get_filename();

        $temporaryPath = $this->create_temporary_directory();

        if (!isset($archiveFileName))
        {
            $archiveFileName = Filesystem::create_unique_name($temporaryPath, uniqid() . '.zip');
        }

        $archiveFilePath = $temporaryPath . $archiveFileName;

        ini_set('memory_limit', '-1');

        $zip = new ZipArchive();
        $zip->open($archiveFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // Create recursive directory iterator
        /** @var \SplFileInfo[] $files */
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($pathToBeZipped), RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file)
        {
            // Skip directories (they would be added automatically)
            if (!$file->isDir())
            {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($pathToBeZipped) + 1);

                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
        }

        return $archiveFilePath;
    }

    /**
     * @param string $file
     * @param boolean $withSafeNames
     *
     * @return string
     */
    public function extract_file($file, bool $withSafeNames = true)
    {
        $extractedFilesDirectory = $this->create_temporary_directory();

        $zipArchive = new ZipArchive();
        $zipArchive->open($file);

        $filesInfo = $this->getFilesInfo($zipArchive);

        foreach ($filesInfo as $fileInfo)
        {
            $zipArchive->extractTo($extractedFilesDirectory, $fileInfo['name']);
        }

        if ($withSafeNames)
        {
            Filesystem::create_safe_names($extractedFilesDirectory);
        }

        return $extractedFilesDirectory;
    }

    /**
     * Retrieves the file information metadata from the zip archive
     *
     * @param \ZipArchive $zipArchive
     *
     * @return string[][]
     */
    protected function getFilesInfo(ZipArchive $zipArchive)
    {
        $filesInfo = [];

        for ($i = 0; $i < $zipArchive->numFiles; $i ++)
        {
            $fileInfo = $zipArchive->statIndex($i);

            if (strpos($fileInfo['name'], '.') === false || strpos($fileInfo['name'], '__MACOSX') !== false)
            {
                continue;
            }

            $filesInfo[] = $fileInfo;
        }

        usort($filesInfo, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        return $filesInfo;
    }

    /**
     * @return string[]
     */
    public function get_supported_mimetypes()
    {
        return array(
            'application/x-zip-compressed',
            'application/zip',
            'multipart/x-zip',
            'application/x-gzip',
            'multipart/x-gzip'
        );
    }

    /**
     * @param string $mimetype
     *
     * @return boolean
     */
    public function is_supported_mimetype($mimetype)
    {
        return in_array($mimetype, $this->get_supported_mimetypes());
    }
}
