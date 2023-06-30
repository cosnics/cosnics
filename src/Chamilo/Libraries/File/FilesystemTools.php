<?php
namespace Chamilo\Libraries\File;

/**
 * @package Chamilo\Libraries\File
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FilesystemTools
{
    /**
     * @param string $fileSize
     *
     * @return int
     */
    public static function interpret_file_size($fileSize)
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

        if (preg_match('#([KMGTP]?B?)$#si', $fileSize, $matches) && !empty($bytesArray[$matches[1]]))
        {
            $bytes *= $bytesArray[$matches[1]];
        }

        $bytes = intval(round($bytes, 2));

        return $bytes;
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
        $index = 0;

        if (!is_null($desiredFilename))
        {
            $filename = self::create_safe_name($desiredFilename);
            $newFilename = $filename;

            while (file_exists($desiredPath . '/' . $newFilename))
            {
                $file_parts = explode('.', $filename);

                if (count($file_parts) > 1)
                {
                    $newFilename = array_shift($file_parts) . ($index ++) . '.' . implode('.', $file_parts);
                }
                else
                {
                    $newFilename = array_shift($file_parts) . ($index ++);
                }
            }

            return $newFilename;
        }

        $uniquePath = dirname($desiredPath) . '/' . Filesystem::create_safe_name(basename($desiredPath));

        while (is_dir($uniquePath))
        {
            $uniquePath = $desiredPath . ($index ++);
        }

        return $uniquePath;
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
        if (is_file($path))
        {
            return filesize($path);
        }

        if (is_dir($path))
        {
            $totalDiskSpace = 0;
            $files = Filesystem::get_directory_content($path, Filesystem::LIST_FILES);

            foreach ($files as $index => $file)
            {
                $totalDiskSpace += @filesize($file);
            }

            return $totalDiskSpace;
        }

        // If path doesn't exist, return null
        return 0;
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
        $handle = tmpfile();
        fwrite($handle, $content);
        $properties = fstat($handle);
        fclose($handle);

        return $properties['size'];
    }

    /**
     * This function detects every uncreated directory of a given path and returns it as an array of paths
     *
     * @param string $path
     *
     * @return string[]
     */
    public static function get_uncreated_directories($path)
    {
        $uncreatedDirectories = [];

        while (!is_dir($path))
        {
            $uncreatedDirectories[] = $path;
            $path = dirname($path);
        }

        return $uncreatedDirectories;
    }
}