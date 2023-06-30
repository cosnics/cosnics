<?php
namespace Chamilo\Libraries\File;

use Chamilo\Libraries\Utilities\StringUtilities;
use DirectoryIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use function Fileperms;

/**
 * This class implements some usefull functions to hanlde the filesystem
 *
 * @package Chamilo\Libraries\File
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
     * @param string $filePath
     * @param string $chmodValue
     * @param bool $recursive
     *
     * @deprecated Use \Symfony\Component\Filesystem\Filesystem::chmod()
     */
    public static function chmod($filePath, $chmodValue, $recursive = false)
    {
        $newChmodValue = null;

        if (is_integer($chmodValue))
        {
            $newChmodValue = (int) $chmodValue;
        }
        elseif (is_string($chmodValue))
        {
            $newChmodValue = intval($chmodValue);
        }

        if (isset($newChmodValue) && file_exists($filePath))
        {
            $newChmodValue = octdec($newChmodValue);

            if (is_dir($filePath) && $recursive)
            {
                $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($filePath));

                foreach ($iterator as $item)
                {
                    chmod($item, $newChmodValue);
                }
            }
            else
            {
                chmod($filePath, $newChmodValue);
            }
        }
    }

    /**
     * Copies a file.
     * If the destination directory doesn't exist, this function tries to create the directory using the
     * Filesystem::create_dir function.
     *
     * @param string $source
     * @param string $destination
     * @param bool $overwrite
     *
     * @return bool
     * @deprecated Use \Symfony\Component\Filesystem\Filesystem::copy()
     */
    public static function copy_file($source, $destination, $overwrite = false)
    {
        if (file_exists($destination) && !$overwrite)
        {
            return false;
        }

        $destination_dir = dirname($destination);

        if (file_exists($source) && Filesystem::create_dir($destination_dir))
        {
            return copy($source, $destination);
        }
    }

    /**
     * Creates a directory.
     * This function creates all missing directories in a given path.
     *
     * @param string $path
     * @param string $mode
     *
     * @return bool
     * @deprecated Use \Symfony\Component\Filesystem\Filesystem::mkdir()
     */
    public static function create_dir($path, $mode = null)
    {
        if (!$mode)
        {
            $mode = 06770;
        }

        // If the given path is a file, return false
        if (is_file($path))
        {
            return false;
        }

        // If the directory doesn't exist yet, create it using php's mkdir
        // function
        if (!is_dir($path))
        {
            $uncreated_directories = FilesystemTools::get_uncreated_directories($path);

            if (!mkdir($path, $mode, true))
            {
                return false;
            }
        }
        else
        {
            $uncreated_directories = [];
        }

        foreach ($uncreated_directories as $path)
        {
            $perms = Fileperms($path);
            $currentPermString = substr(decoct($perms), - 4);
            $targetPermString = decoct($mode);
            // only try to chmod if needed
            // chmod often needs us to be owner which is sometimes problematic with
            // mounted filesystem
            if ($currentPermString != $targetPermString)
            {
                if (!chmod($path, $mode))
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Creates a safe name for a file or directory
     *
     * @param string $desiredName
     *
     * @return string
     */
    public static function create_safe_name($desiredName)
    {
        $asciiString = StringUtilities::getInstance()->createString($desiredName)->toAscii()->__toString();

        return preg_replace('/[:;!\x20\x2F\x5C]/', '_', $asciiString);
    }

    /**
     * Scans all files and directories in the given path and subdirectories.
     * If a file or directory name isn't
     * considered as safe, it will be renamed to a safe name.
     *
     * @param string $path
     */
    public static function create_safe_names($path)
    {
        $list = Filesystem::get_directory_content($path);

        // Sort everything, so renaming a file or directory has no impact on
        // next elements in the array
        rsort($list);

        foreach ($list as $index => $entry)
        {
            if (basename($entry) != Filesystem::create_safe_name(basename($entry)))
            {
                if (is_file($entry))
                {
                    $safeName = Filesystem::create_unique_name(dirname($entry), basename($entry));
                    $destination = dirname($entry) . '/' . $safeName;
                    Filesystem::copy_file($entry, $destination);
                    unlink($entry);
                }
                elseif (is_dir($entry))
                {
                    $safeName = Filesystem::create_unique_name($entry);
                    rename($entry, $safeName);
                }
            }
        }
    }

    /**
     * Creates a unique name for a file or a directory.
     * This function will also use the function
     * Filesystem::create_safe_name to make sure the resulting name is safe to use.
     *
     * @param string $desiredPath
     * @param string $desiredFilename
     *
     * @return string
     */
    public static function create_unique_name($desiredPath, $desiredFilename = null)
    {
        return FilesystemTools::create_unique_name($desiredPath, $desiredFilename);
    }

    /**
     * This function streams a file to the client
     *
     * @param string $fullFileName
     * @param bool $forced
     * @param string $name
     *
     * @return bool
     * @deprecated Use a BinaryFileResponse()
     */
    public static function file_send_for_download($fullFileName, $forced = false, $name = '', $contentType = '')
    {
        if (!is_file($fullFileName))
        {
            return false;
        }

        $filename = ($name == '') ? basename($fullFileName) : $name;
        $len = filesize($fullFileName);

        if ($forced)
        {
            // force the browser to save the file instead of opening it
            if ($contentType)
            {
                header('Content-type: ' . $contentType);
            }
            else
            {
                header('Content-type: application/octet-stream');
            }

            header('Content-length: ' . (string) $len);

            if (preg_match('/MSIE 5.5/', $_SERVER['HTTP_USER_AGENT']))
            {
                header('Content-Disposition: filename= ' . $filename);
            }
            else
            {
                header('Content-Disposition: attachment; filename= "' . $filename . '"');
            }

            if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
            {
                header('Pragma: ');
                header('Cache-Control: ');
                header('Cache-Control: public'); // IE cannot download from sessions without a cache
            }

            header('Content-Description: ' . $filename);
            header('Content-transfer-encoding: binary');

            ob_clean();
            flush();
            readfile($fullFileName);

            return true;
        }
        else
        {
            // no forced download, just let the browser decide what to do
            // according to the mimetype
            // $content_type = DocumentManager::file_get_mime_type($filename);
            header('Expires: Wed, 01 Jan 1990 00:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

            if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
            {
                header('Pragma: ');
                header('Cache-Control: ');
            }
            else
            {
                header('Cache-Control: no-cache, must-revalidate');
                header('Pragma: no-cache');
            }

            if ($contentType)
            {
                header('Content-type: ' . $contentType);
            }

            header('Content-Length: ' . $len);
            $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);

            if (strpos($user_agent, 'MSIE'))
            {
                header('Content-Disposition: ; filename= ' . $filename);
            }
            else
            {
                header('Content-Disposition: inline; filename= "' . $filename . '"');
            }

            readfile($fullFileName);

            return true;
        }
    }

    /**
     * Transform the file size in a human readable format.
     *
     * @param int $fileSize
     * @param bool $postfix
     *
     * @return int
     */
    public static function format_file_size($fileSize, $postfix = true)
    {
        // Todo: Megabyte vs Mebibyte...
        $kilobyte = 1024;
        $megabyte = pow($kilobyte, 2);
        $gigabyte = pow($kilobyte, 3);

        if ($fileSize >= $gigabyte)
        {
            $fileSize = round($fileSize / $gigabyte * 100) / 100 . ($postfix ? ' GB' : '');
        }
        elseif ($fileSize >= $megabyte)
        {
            $fileSize = round($fileSize / $megabyte * 100) / 100 . ($postfix ? ' MB' : '');
        }
        elseif ($fileSize >= $kilobyte)
        {
            $fileSize = round($fileSize / $kilobyte * 100) / 100 . ($postfix ? ' kB' : '');
        }
        else
        {
            $fileSize = $fileSize . ($postfix ? ' B' : '');
        }

        return $fileSize;
    }

    /**
     * Retrieves all contents (files and/or directories) of a directory
     *
     * @param string $path
     * @param int $type
     * @param bool $recursive
     *
     * @return string[]
     * @deprecated Use \Symfony\Component\Finder\Finder
     */
    public static function get_directory_content(
        $path, $type = Filesystem::LIST_FILES_AND_DIRECTORIES, $recursive = true
    )
    {
        $result = [];

        if (!file_exists($path))
        {
            return $result;
        }

        if ($recursive)
        {
            $it = new RecursiveDirectoryIterator($path);
            $it = new RecursiveIteratorIterator($it, 1);
        }
        else
        {
            $it = new DirectoryIterator($path);
        }

        foreach ($it as $entry)
        {
            if ($it->isDot())
            {
                continue;
            }

            if (($type == Filesystem::LIST_FILES_AND_DIRECTORIES || $type == Filesystem::LIST_FILES) &&
                $entry->isFile())
            {
                // getRealPath() results in php-error in older PHP5 versions
                // $result[] = $entry->getRealPath();
                $result[] = $entry->__toString();
            }

            if (($type == Filesystem::LIST_FILES_AND_DIRECTORIES || $type == Filesystem::LIST_DIRECTORIES) &&
                $entry->isDir())
            {
                // getRealPath() results in php-error in older PHP5 versions
                // $result[] = $entry->getRealPath();
                $result[] = $entry->__toString();
            }
        }

        return $result;
    }

    /**
     * Determines the number of bytes taken by a given directory or file
     *
     * @param string $path
     *
     * @return int
     */
    public static function get_disk_space($path)
    {
        return FilesystemTools::get_disk_space($path);
    }

    /**
     * Guesses the disk space used when the given content would be written to a file
     *
     * @param string $content
     *
     * @return int
     */
    public static function guess_disk_space($content)
    {
        return FilesystemTools::guess_disk_space($content);
    }

    /**
     * @param string $fileSize
     *
     * @return int
     */
    public static function interpret_file_size($fileSize)
    {
        return FilesystemTools::interpret_file_size($fileSize);
    }

    /**
     * Moves a file.
     * If the destination directory doesn't exist, this function tries to create the directory using the
     * Filesystem::create_dir function. Path cannot have a '/' at the end
     *
     * @param string $source
     * @param string $destination
     * @param bool $overwrite
     *
     * @return bool
     * @deprecated Use \Symfony\Component\Filesystem\Filesystem::rename()
     */
    public static function move_file($source, $destination, $overwrite = false)
    {
        if (file_exists($destination) && !$overwrite)
        {
            return false;
        }

        $destinationDirectory = dirname($destination);

        if (file_exists($source) && Filesystem::create_dir($destinationDirectory))
        {
            return rename($source, $destination);
        }

        return false;
    }

    /**
     * Made a recursive copy function to copy entire directories
     *
     * @param string $source
     * @param string $destination
     * @param bool $overwrite
     *
     * @return bool
     * @deprecated Use \Symfony\Component\Filesystem\Filesystem::mirror()
     */
    public static function recurse_copy($source, $destination, $overwrite = false)
    {
        if (!is_dir($source))
        {
            return self::copy_file($source, $destination, $overwrite);
        }

        $bool = true;

        $content = self::get_directory_content($source, self::LIST_FILES_AND_DIRECTORIES, false);

        foreach ($content as $file)
        {
            $pathToFile = $source . '/' . $file;
            $pathToNewFile = $destination . '/' . $file;

            if (!is_dir($pathToFile))
            {
                $bool &= self::copy_file($pathToFile, $pathToNewFile, $overwrite);
            }
            else
            {
                self::create_dir($pathToNewFile);
                $bool &= self::recurse_copy($pathToFile, $pathToNewFile, $overwrite);
            }
        }

        return $bool;
    }

    /**
     * @param string $source
     * @param string $destination
     * @param bool $overwrite
     *
     * @return bool
     * @deprecated Use \Symfony\Component\Filesystem\Filesystem::rename()
     */
    public static function recurse_move($source, $destination, $overwrite = false)
    {
        if (!is_dir($source))
        {
            return self::move_file($source, $destination, $overwrite);
        }

        $bool = true;
        $content = self::get_directory_content($source, self::LIST_FILES_AND_DIRECTORIES, false);

        foreach ($content as $file)
        {
            $pathToFile = $source . '/' . $file;
            $pathToNewFile = $destination . '/' . $file;

            if (!is_dir($pathToFile))
            {
                $bool &= self::move_file($pathToFile, $pathToNewFile, $overwrite);
            }
            else
            {
                self::create_dir($pathToNewFile);
                $bool &= self::recurse_move($pathToFile, $pathToNewFile, $overwrite);
            }
        }

        $bool &= @rmdir($source);

        return $bool;
    }

    /**
     * Removes a file or a directory (and all its contents).
     *
     * @param string $path
     *
     * @return bool
     * @deprecated Use \Symfony\Component\Filesystem\Filesystem::remove()
     */
    public static function remove($path)
    {
        if (realpath($path) == '/')
        {
            return false;
        }

        if (is_file($path))
        {
            return @unlink($path);
        }
        elseif (is_dir($path))
        {
            $content = Filesystem::get_directory_content($path);
            // Reverse sort the content so deepest entries come first.
            rsort($content);
            $result = true;

            foreach ($content as $index => $entry)
            {
                if (is_file($entry))
                {
                    $result &= @unlink($entry);
                }
                elseif (is_dir($entry))
                {
                    $result &= @rmdir($entry);
                }
            }

            return ($result & @rmdir($path));
        }

        return false;
    }

    /**
     * Writes content to a file.
     * This function will try to create the path and the file if they don't exist yet.
     *
     * @param string $file
     * @param string $content
     * @param bool $append
     *
     * @return bool
     * @deprecated Use \Symfony\Component\Filesystem\Filesystem::appendToFile() or
     *             \Symfony\Component\Filesystem\Filesystem::dumpFile()
     */
    public static function write_to_file($file, $content, $append = false)
    {
        if (Filesystem::create_dir(dirname($file)))
        {
            if ($createFile = fopen($file, $append ? 'a' : 'w'))
            {
                fwrite($createFile, $content);
                fclose($createFile);
                chmod($file, 0777);

                return true;
            }

            return false;
        }

        return false;
    }
}
