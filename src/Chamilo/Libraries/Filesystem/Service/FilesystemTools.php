<?php
namespace Chamilo\Libraries\Filesystem\Service;

use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\Iterator\FileTypeFilterIterator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * @package Chamilo\Libraries\Filesystem\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FilesystemTools
{
    protected Filesystem $filesystem;

    protected StringUtilities $stringUtilities;

    public function __construct(
        Filesystem $filesystem, StringUtilities $stringUtilities
    )
    {
        $this->filesystem = $filesystem;
        $this->stringUtilities = $stringUtilities;
    }

    public function createSafeName(string $desiredName): string
    {
        $asciiString = $this->getStringUtilities()->createString($desiredName)->toAscii()->__toString();

        return preg_replace('/[:;!\x20\x2F\x5C]/', '_', $asciiString);
    }

    public function createSafeNames(string $path): void
    {
        $filesystem = $this->getFilesystem();
        $list = $this->getDirectoryContent($path);

        // Sort everything, so renaming a file or directory has no impact on
        // next elements in the array
        $list->reverseSorting();

        foreach ($list as $entry) {
            if (basename($entry) != $this->createSafeName(basename($entry))) {
                if (is_file($entry)) {
                    $safeName = $this->createUniqueName(dirname($entry), basename($entry));
                    $destination = dirname($entry) . '/' . $safeName;

                    $filesystem->copy($entry, $destination);
                    $filesystem->remove($entry);
                }
                elseif (is_dir($entry)) {
                    $safeName = $this->createUniqueName($entry);
                    $filesystem->rename($entry, $safeName);
                }
            }
        }
    }

    public function createUniqueName(string $desiredPath, ?string $desiredFilename = null): string
    {
        $index = 0;

        if (!is_null($desiredFilename)) {
            $filename = $this->createSafeName($desiredFilename);
            $newFilename = $filename;

            while (file_exists($desiredPath . '/' . $newFilename)) {
                $fileParts = explode('.', $filename);

                if (count($fileParts) > 1) {
                    $newFilename = array_shift($fileParts) . ($index ++) . '.' . implode('.', $fileParts);
                }
                else {
                    $newFilename = array_shift($fileParts) . ($index ++);
                }
            }

            return $newFilename;
        }

        $uniquePath = dirname($desiredPath) . '/' . $this->createSafeName(basename($desiredPath));

        while (is_dir($uniquePath)) {
            $uniquePath = $desiredPath . ($index ++);
        }

        return $uniquePath;
    }

    public function formatFileSize(int $fileSize, bool $postfix = true): string
    {
        $kilobyte = 1024;
        $megabyte = pow($kilobyte, 2);
        $gigabyte = pow($kilobyte, 3);

        if ($fileSize >= $gigabyte) {
            $fileSize = round($fileSize / $gigabyte * 100) / 100 . ($postfix ? ' GB' : '');
        }
        elseif ($fileSize >= $megabyte) {
            $fileSize = round($fileSize / $megabyte * 100) / 100 . ($postfix ? ' MB' : '');
        }
        elseif ($fileSize >= $kilobyte) {
            $fileSize = round($fileSize / $kilobyte * 100) / 100 . ($postfix ? ' kB' : '');
        }
        else {
            $fileSize = $fileSize . ($postfix ? ' B' : '');
        }

        return $fileSize;
    }

    public function getDirectoryContent(
        string $path, ?int $type = null, bool $recursive = true
    ): Finder
    {
        $finder = new Finder();

        if (!$recursive) {
            $finder->depth('== 0');
        }

        if ($type == FileTypeFilterIterator::ONLY_FILES) {
            $finder->files();
        }
        elseif ($type == FileTypeFilterIterator::ONLY_DIRECTORIES) {
            $finder->directories();
        }

        return $finder->in($path);
    }

    /**
     * Determines the number of bytes taken by a given directory or file
     */
    public function getDiskSpace(string $path): int
    {
        if (is_file($path)) {
            return filesize($path);
        }

        if (is_dir($path)) {
            $totalDiskSpace = 0;
            $files = $this->getDirectoryContent($path, FileTypeFilterIterator::ONLY_FILES);

            foreach ($files as $file) {
                $totalDiskSpace += filesize($file);
            }

            return $totalDiskSpace;
        }

        // If path doesn't exist, return null
        return 0;
    }

    public function getFileForDownloadResponse(
        string $fullFileName, ?string $name = null, ?string $contentType = null
    ): BinaryFileResponse
    {
        $filename = $name ?: basename($fullFileName);

        $binaryFileResponse = new BinaryFileResponse($fullFileName);
        $binaryFileResponse->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);

        $binaryFileResponse->headers->set('Content-type', $contentType ?: 'application/octet-stream');
        $binaryFileResponse->headers->set('Content-Description', $filename);
        $binaryFileResponse->headers->set('Content-transfer-encoding', 'binary');

        return $binaryFileResponse;
    }

    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    /**
     * Guesses the disk space used when the given content would be written to a file
     */
    public function guessDiskSpace(string $content): int
    {
        $handle = tmpfile();
        fwrite($handle, $content);
        $properties = fstat($handle);
        fclose($handle);

        return $properties['size'];
    }

    public function interpretFileSize(string $fileSize): int
    {
        $bytesArray = [
            'B' => 1,
            'KB' => 1024,
            'MB' => pow(1024, 2),
            'GB' => pow(1024, 3),
            'TB' => pow(1024, 4),
            'PB' => pow(1024, 5),
            'K' => 1024,
            'M' => pow(1024, 2),
            'G' => pow(1024, 3),
            'T' => pow(1024, 4),
            'P' => pow(1024, 5)
        ];

        $bytes = floatval($fileSize);

        if (preg_match('#([KMGTP]?B?)$#i', $fileSize, $matches) && !empty($bytesArray[$matches[1]])) {
            $bytes *= $bytesArray[$matches[1]];
        }

        return intval(round($bytes, 2));
    }
}