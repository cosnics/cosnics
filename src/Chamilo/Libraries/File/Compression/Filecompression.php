<?php
namespace Chamilo\Libraries\File\Compression;

use Chamilo\Libraries\File\Compression\Pclzip\PclzipFilecompression;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;

/**
 *
 * @package common.filecompression
 */
/**
 * An abstract class for handling file compression.
 * Impement new compression methods by creating a class which extends
 * this abstract class.
 */
abstract class Filecompression
{

    private $filename;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Creates a temporary directory in which the file can be extracted
     *
     * @return string The full path to the created directory
     * @todo Put this function in filesystem class
     */
    protected function create_temporary_directory()
    {
        $path = Path::getInstance()->getTemporaryPath(__NAMESPACE__) . uniqid() . DIRECTORY_SEPARATOR;
        Filesystem::create_dir($path);
        return $path;
    }

    /**
     * Retrieves an array of all supported mimetypes for this file compression implementation.
     *
     * @return array
     */
    abstract public function get_supported_mimetypes();

    /**
     * Determines if a given mimetype is supported by the file compression implementation.
     *
     * @return boolean True if the given mimetype is supported.
     */
    abstract public function is_supported_mimetype($mimetype);

    /**
     * Extracts a compressed file to a given directory.
     * This function will also make sure that all resulting directory-
     * and filenames are safe using the Filesystem::create_safe_names function.
     *
     * @see Filesystem::create_safe_names
     * @param string $file The full path to the file which should be extracted
     * @return string boolean full path to the directory where the file was extracted or boolean false if extraction
     *         wasn't successfull
     */
    abstract public function extract_file($file);

    /**
     * Creates an archive containing all contents from the given directory.
     *
     * @param string $path The full path to the content that should be stored in the archive.
     * @return string The full path to the created archive file.
     */
    abstract public function create_archive($path);

    /**
     * Create a filecompression instance
     *
     * @todo At the moment this returns the class using pclzip. The class to return should be configurable
     */
    public static function factory()
    {
        return new PclzipFilecompression();
    }

    public function set_filename($filename, $file_extension = 'cpo')
    {
        $this->filename = Filesystem::create_safe_name($filename) . '.' . $file_extension;
    }

    public function get_filename()
    {
        return $this->filename;
    }
}
