<?php
namespace Chamilo\Libraries\File;

use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Symfony\Component\Finder\Iterator\FileTypeFilterIterator;

/**
 * This class implements some usefull functions to hanlde the filesystem
 *
 * @package    Chamilo\Libraries\File
 * @deprecated Use \Symfony\Component\Filesystem\Filesystem or \Chamilo\Libraries\File\FilesystemTools or
 *             \Symfony\Component\Finder\Finder
 */
class Filesystem
{
    /**
     * Constant representing "Directories"
     */
    public const LIST_DIRECTORIES = 3;

    /**
     * Constant representing "Files"
     */
    public const LIST_FILES = 2;

    /**
     * Constant representing "Files and directories"
     */
    public const LIST_FILES_AND_DIRECTORIES = 1;

    /**
     * Call the chmod function on the given file path.
     * The chmod value must be the octal value, with or without its
     * leading zero Ex: Filesystem::chmod('/path/to/file', '666') OK Filesystem::chmod('/path/to/file', '0666') OK
     * Filesystem::chmod('/path/to/file', 666) OK Filesystem::chmod('/path/to/file', 0666) OK Note: This function
     * was written to facilitate the storage of a chmod value. The PHP chmod value must be called with an octal number,
     * but it is not easy to store a value with a leading 0 that is a number and not a string.
     *
     * @deprecated Use \Symfony\Component\Filesystem\Filesystem::chmod()
     */
    public static function chmod(string $filePath, int $chmodValue, bool $recursive = false): void
    {
        self::getFilesystem()->chmod($filePath, $chmodValue, 0000, $recursive);
    }

    /**
     * Copies a file.
     * If the destination directory doesn't exist, this function tries to create the directory using the
     * Filesystem::create_dir function.
     *
     * @deprecated Use \Symfony\Component\Filesystem\Filesystem::copy()
     */
    public static function copy_file(string $source, string $destination, bool $overwrite = false): void
    {
        self::getFilesystem()->copy($source, $destination, $overwrite);
    }

    /**
     * Creates a directory.
     * This function creates all missing directories in a given path.
     *
     * @deprecated Use \Symfony\Component\Filesystem\Filesystem::mkdir()
     */
    public static function create_dir(string $path, int $mode = 0777): void
    {
        self::getFilesystem()->mkdir($path, $mode);
    }

    /**
     * Creates a safe name for a file or directory
     *
     * @deprecated Use \Chamilo\Libraries\File\FilesystemTools::createSafeName()
     */
    public static function create_safe_name(string $desiredName): string
    {
        return self::getFilesystemTools()->createSafeName($desiredName);
    }

    /**
     * Scans all files and directories in the given path and subdirectories.
     * If a file or directory name isn't considered as safe, it will be renamed to a safe name.
     *
     * @deprecated Use \Chamilo\Libraries\File\FilesystemTools::createSafeNames()
     */
    public static function create_safe_names(string $path): void
    {
        self::getFilesystemTools()->createSafeNames($path);
    }

    /**
     * Creates a unique name for a file or a directory.
     * This function will also use the function
     * Filesystem::create_safe_name to make sure the resulting name is safe to use.
     *
     * @deprecated Use \Chamilo\Libraries\File\FilesystemTools::createUniqueName()
     */
    public static function create_unique_name(string $desiredPath, ?string $desiredFilename = null): string
    {
        return self::getFilesystemTools()->createUniqueName($desiredPath, $desiredFilename);
    }

    /**
     * This function streams a file to the client
     *
     * @deprecated Use \Chamilo\Libraries\File\FilesystemTools::fileSendForDownload() or BinaryFileResponse() directly
     */
    public static function file_send_for_download(
        string $fullFileName, ?string $name = null, ?string $contentType = null
    ): void
    {
        self::getFilesystemTools()->sendFileForDownload($fullFileName, $name, $contentType);
    }

    /**
     * Transform the file size in a human readable format.
     *
     * @deprecated Use \Chamilo\Libraries\File\FilesystemTools::formatFileSize()
     */
    public static function format_file_size(int $fileSize, bool $postfix = true): string
    {
        return self::getFilesystemTools()->formatFileSize($fileSize, $postfix);
    }

    public static function getFilesystem(): \Symfony\Component\Filesystem\Filesystem
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            \Symfony\Component\Filesystem\Filesystem::class
        );
    }

    public static function getFilesystemTools(): FilesystemTools
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            FilesystemTools::class
        );
    }

    /**
     * Retrieves all contents (files and/or directories) of a directory
     *
     * @return string[]
     * @deprecated Use \Chamilo\Libraries\File\FilesystemTools::getDirectoryContent() or
     *             \Symfony\Component\Finder\Finder directly
     */
    public static function get_directory_content(
        string $path, int $type = self::LIST_FILES_AND_DIRECTORIES, bool $recursive = true
    ): array
    {
        $iteratorType = null;

        switch ($type)
        {
            case self::LIST_FILES:
                $iteratorType = FileTypeFilterIterator::ONLY_FILES;
                break;
            case self::LIST_DIRECTORIES:
                $iteratorType = FileTypeFilterIterator::ONLY_DIRECTORIES;
        }

        return iterator_to_array(self::getFilesystemTools()->getDirectoryContent($path, $iteratorType, $recursive));
    }

    /**
     * Determines the number of bytes taken by a given directory or file
     *
     * @deprecated Use \Chamilo\Libraries\File\FilesystemTools::getDiskSpace()
     */
    public static function get_disk_space(string $path): int
    {
        return self::getFilesystemTools()->getDiskSpace($path);
    }

    /**
     * Guesses the disk space used when the given content would be written to a file
     *
     * @deprecated Use \Chamilo\Libraries\File\FilesystemTools::guessDiskSpace()
     */
    public static function guess_disk_space(string $content): int
    {
        return self::getFilesystemTools()->guessDiskSpace($content);
    }

    /**
     * @deprecated Use \Chamilo\Libraries\File\FilesystemTools::interpretFileSize()
     */
    public static function interpret_file_size(string $fileSize): int
    {
        return self::getFilesystemTools()->interpretFileSize($fileSize);
    }

    /**
     * Moves a file.
     * If the destination directory doesn't exist, this function tries to create the directory using the
     * Filesystem::create_dir function. Path cannot have a '/' at the end
     *
     * @deprecated Use \Symfony\Component\Filesystem\Filesystem::rename()
     */
    public static function move_file(string $source, string $destination, bool $overwrite = false): void
    {
        self::getFilesystem()->rename($source, $destination, $overwrite);
    }

    /**
     * Made a recursive copy function to copy entire directories
     *
     * @deprecated Use \Symfony\Component\Filesystem\Filesystem::mirror()
     */
    public static function recurse_copy(string $source, string $destination): void
    {
        self::getFilesystem()->mirror($source, $destination);
    }

    /**
     * @deprecated Use \Symfony\Component\Filesystem\Filesystem::rename()
     */
    public static function recurse_move(string $source, string $destination, bool $overwrite = false): void
    {
        self::getFilesystem()->rename($source, $destination, $overwrite);
    }

    /**
     * Removes a file or a directory (and all its contents).
     *
     * @deprecated Use \Symfony\Component\Filesystem\Filesystem::remove()
     */
    public static function remove(string $path): void
    {
        self::getFilesystem()->remove($path);
    }

    /**
     * Writes content to a file.
     * This function will try to create the path and the file if they don't exist yet.
     *
     * @deprecated Use \Symfony\Component\Filesystem\Filesystem::appendToFile() or
     *             \Symfony\Component\Filesystem\Filesystem::dumpFile()
     */
    public static function write_to_file(string $file, string $content, bool $append = false): void
    {
        $filesystem = self::getFilesystem();

        if ($append)
        {
            $filesystem->appendToFile($file, $content);
        }
        else
        {
            $filesystem->dumpFile($file, $content);
        }
    }
}
