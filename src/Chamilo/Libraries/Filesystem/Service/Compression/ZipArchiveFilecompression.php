<?php
namespace Chamilo\Libraries\Filesystem\Service\Compression;

use Chamilo\Libraries\Filesystem\Service\ConfigurablePathBuilder;
use Chamilo\Libraries\Filesystem\Service\FilesystemTools;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Filesystem\Filesystem;
use ZipArchive;

/**
 * @package Chamilo\Libraries\Filesystem\Service\Compression
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ZipArchiveFilecompression
{
    public function __construct(
        protected Filesystem $filesystem, protected FilesystemTools $filesystemTools,
        protected ConfigurablePathBuilder $configurablePathBuilder
    )
    {
    }

    public function createArchive(string $path, ?string $fileName = null, string $fileExtension = 'cpo'): string
    {
        $pathToBeZipped = realpath($path);
        $temporaryPath = $this->createTemporaryDirectory();

        if (!isset($fileName)) {
            $fileName = $this->filesystemTools->createUniqueName($temporaryPath, uniqid());
        }

        $archiveFileName = $this->filesystemTools->createSafeName($fileName) . '.' . $fileExtension;

        $archiveFilePath = $temporaryPath . $archiveFileName;

        ini_set('memory_limit', '-1');

        $zip = new ZipArchive();
        $zip->open($archiveFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // Create recursive directory iterator
        /** @var \SplFileInfo[] $files */
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($pathToBeZipped), RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            // Skip directories (they would be added automatically)
            if (!$file->isDir()) {
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
        $path = $this->configurablePathBuilder->getTemporaryPath(__NAMESPACE__) . uniqid() . DIRECTORY_SEPARATOR;
        $this->filesystem->mkdir($path);

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

        foreach ($filesInfo as $fileInfo) {
            $zipArchive->extractTo($extractedFilesDirectory, $fileInfo['name']);
        }

        if ($withSafeNames) {
            $this->filesystemTools->createSafeNames($extractedFilesDirectory);
        }

        return $extractedFilesDirectory;
    }

    /**
     * @return string[][]
     */
    protected function getFilesInfo(ZipArchive $zipArchive): array
    {
        $filesInfo = [];

        for ($fileIndex = 0; $fileIndex < $zipArchive->numFiles; $fileIndex ++) {
            $fileInfo = $zipArchive->statIndex($fileIndex);

            if (!str_contains($fileInfo['name'], '.') || str_contains($fileInfo['name'], '__MACOSX')) {
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
