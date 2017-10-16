<?php
namespace Chamilo\Libraries\File\Compression\Pclzip;

use Chamilo\Libraries\File\Compression\Filecompression;
use Chamilo\Libraries\File\Filesystem;
use PclZip;

/**
 * This class implements file compression and extraction using the PclZip library
 *
 * @package Chamilo\Libraries\File\Compression\Pclzip
 */
class PclzipFilecompression extends Filecompression
{

    public function __construct()
    {
        $tmpDir = sys_get_temp_dir() . '/zip/';

        if (! file_exists($tmpDir))
        {
            mkdir($tmpDir);
        }

        define('PCLZIP_TEMPORARY_DIR', $tmpDir);
    }

    /**
     *
     * @see \Chamilo\Libraries\File\Compression\Filecompression::get_supported_mimetypes()
     */
    public function get_supported_mimetypes()
    {
        return array(
            'application/x-zip-compressed',
            'application/zip',
            'multipart/x-zip',
            'application/x-gzip',
            'multipart/x-gzip');
    }

    /**
     *
     * @see \Chamilo\Libraries\File\Compression\Filecompression::is_supported_mimetype()
     */
    public function is_supported_mimetype($mimetype)
    {
        return in_array($mimetype, $this->get_supported_mimetypes());
    }

    /**
     *
     * @see \Chamilo\Libraries\File\Compression\Filecompression::extract_file()
     */
    public function extract_file($file, $withSafeNames = true)
    {
        $dir = $this->create_temporary_directory();
        $pclzip = new PclZip($file);

        if ($pclzip->extract(PCLZIP_OPT_PATH, $dir, PCLZIP_OPT_ADD_TEMP_FILE_ON) == 0)
        {
            print_r($pclzip->errorInfo());
            return false;
        }

        if ($withSafeNames)
        {
            Filesystem::create_safe_names($dir);
        }

        return $dir;
    }

    /**
     *
     * @see \Chamilo\Libraries\File\Compression\Filecompression::create_archive()
     */
    public function create_archive($path)
    {
        $fileName = $this->get_filename();
        $temporaryPath = $this->create_temporary_directory();

        if (! isset($fileName))
        {
            $fileName = Filesystem::create_unique_name($temporaryPath, uniqid() . '.zip');
        }

        $path = realpath($path);

        $archiveFile = $temporaryPath . $fileName;

        $fileList = Filesystem::get_directory_content($path, Filesystem::LIST_FILES, true);

        ini_set('memory_limit', '-1');

        $pclzip = new PclZip($archiveFile);
        $pclzip->add($fileList, PCLZIP_OPT_REMOVE_PATH, $path, PCLZIP_OPT_ADD_TEMP_FILE_ON);

        return $archiveFile;
    }
}
