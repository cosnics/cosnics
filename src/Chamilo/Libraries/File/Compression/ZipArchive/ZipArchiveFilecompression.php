<?php
namespace Chamilo\Libraries\File\Compression\ZipArchive;

use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\FilesystemTools;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Filesystem\Filesystem;
use ZipArchive;

/**
 * @package Chamilo\Libraries\File\Compression\ZipArchive
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ZipArchiveFilecompression
{
    protected ConfigurablePathBuilder $configurablePathBuilder;

    protected Filesystem $filesystem;

    protected FilesystemTools $filesystemTools;

    public function __construct(
        Filesystem $filesystem, FilesystemTools $filesystemTools, ConfigurablePathBuilder $configurablePathBuilder
    )
    {
        $this->filesystem = $filesystem;
        $this->filesystemTools = $filesystemTools;
        $this->configurablePathBuilder = $configurablePathBuilder;
    }

    public function createArchive(string $path, ?string $fileName = null, string $fileExtension = 'cpo'): string
    {
        $filesystemTools = $this->getFilesystemTools();

        $pathToBeZipped = realpath($path);
        $temporaryPath = $this->createTemporaryDirectory();

        if (!isset($fileName))
        {
            $fileName = $filesystemTools->createUniqueName($temporaryPath, uniqid());
        }

        $archiveFileName = $filesystemTools->createSafeName($fileName) . '.' . $fileExtension;

        $archiveFilePath = $temporaryPath . $archiveFileName;

        ini_set('memory_limit', '-1');

        $zip = new ZipArchive();
        $zip->open($archiveFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // Create recursive directory iterator
        /** @var \SplFileInfo[] $files */
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($pathToBeZipped), RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file)
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

    protected function createTemporaryDirectory(): string
    {
        $path = $this->getConfigurablePathBuilder()->getTemporaryPath(__NAMESPACE__) . uniqid() . DIRECTORY_SEPARATOR;
        $this->getFilesystem()->mkdir($path);

        return $path;
    }

    /**
     * Extracts a compressed file to a given directory.
     * This function will also make sure that all resulting directory-
     * and filenames are safe using the FilesystemTools::createSafeNames function.
     *
     * @param string $file The full path to the file which should be extracted
     *
     * @return string boolean full path to the directory where the file was extracted or boolean false if extraction
     *         wasn't successfull
     * @see FilesystemTools::createSafeNames
     */
    public function extractFile(string $file, bool $withSafeNames = true): string
    {
        $extractedFilesDirectory = $this->createTemporaryDirectory();

        $zipArchive = new ZipArchive();
        $zipArchive->open($file);

        $filesInfo = $this->getFilesInfo($zipArchive);

        foreach ($filesInfo as $fileInfo)
        {
            $zipArchive->extractTo($extractedFilesDirectory, $fileInfo['name']);
        }

        if ($withSafeNames)
        {
            $this->getFilesystemTools()->createSafeNames($extractedFilesDirectory);
        }

        return $extractedFilesDirectory;
    }

    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->configurablePathBuilder;
    }

    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    public function getFilesystemTools(): FilesystemTools
    {
        return $this->filesystemTools;
    }

    /**
     * Retrieves the file information metadata from the zip archive
     *
     * @return string[][]
     */
    protected function getFilesInfo(ZipArchive $zipArchive): array
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
     * Retrieves an array of all supported mimetypes for this file compression implementation.
     *
     * @return string[]
     */
    public function getSupportedMimetypes(): array
    {
        return [
            'application/x-zip-compressed',
            'application/zip',
            'multipart/x-zip',
            'application/x-gzip',
            'multipart/x-gzip'
        ];
    }

    public function isSupportedMimetype(string $mimetype): bool
    {
        return in_array($mimetype, $this->getSupportedMimetypes());
    }
}
