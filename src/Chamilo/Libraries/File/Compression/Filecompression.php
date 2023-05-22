<?php
namespace Chamilo\Libraries\File\Compression;

use Chamilo\Libraries\File\Compression\ZipArchive\ZipArchiveFilecompression;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;

/**
 * An abstract class for handling file compression.
 * Impement new compression methods by creating a class which extends
 * this abstract class.
 *
 * @package Chamilo\Libraries\File\Compression
 */
abstract class Filecompression
{

    /**
     * @var string
     */
    private $filename;

    /**
     * Creates an archive containing all contents from the given directory.
     *
     * @param string $path The full path to the content that should be stored in the archive.
     *
     * @return string The full path to the created archive file.
     */
    abstract public function create_archive($path);

    /**
     * Creates a temporary directory in which the file can be extracted
     *
     * @return string The full path to the created directory
     */
    protected function create_temporary_directory()
    {
        $path = Path::getInstance()->getTemporaryPath(__NAMESPACE__) . uniqid() . DIRECTORY_SEPARATOR;
        Filesystem::create_dir($path);

        return $path;
    }

    /**
     * Extracts a compressed file to a given directory.
     * This function will also make sure that all resulting directory-
     * and filenames are safe using the Filesystem::create_safe_names function.
     *
     * @param string $file The full path to the file which should be extracted
     *
     * @return string boolean full path to the directory where the file was extracted or boolean false if extraction
     *         wasn't successfull
     * @see Filesystem::create_safe_names
     */
    abstract public function extract_file($file);

    /**
     * Create a filecompression instance
     *
     * @return \Chamilo\Libraries\File\Compression\ZipArchive\ZipArchiveFilecompression
     */
    public static function factory()
    {
        return new ZipArchiveFilecompression();
    }

    /**
     * @return string
     */
    public function get_filename()
    {
        return $this->filename;
    }

    /**
     * Retrieves an array of all supported mimetypes for this file compression implementation.
     *
     * @return string[]
     */
    abstract public function get_supported_mimetypes();

    /**
     * Determines if a given mimetype is supported by the file compression implementation.
     *
     * @param string $mimetype
     *
     * @return bool True if the given mimetype is supported.
     */
    abstract public function is_supported_mimetype($mimetype);

    /**
     * @param string $filename
     * @param string $fileExtension
     */
    public function set_filename($filename, $fileExtension = 'cpo')
    {
        $this->filename = Filesystem::create_safe_name($filename) . '.' . $fileExtension;
    }
}
