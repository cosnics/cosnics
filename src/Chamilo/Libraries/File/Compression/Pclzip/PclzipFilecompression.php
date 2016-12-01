<?php
namespace Chamilo\Libraries\File\Compression\Pclzip;

use Chamilo\Libraries\File\Compression\Filecompression;
use Chamilo\Libraries\File\Filesystem;
use PclZip;

/**
 * This class implements file compression and extraction using the PclZip library
 */
class PclzipFilecompression extends Filecompression
{

    public function get_supported_mimetypes()
    {
        return array(
            'application/x-zip-compressed', 
            'application/zip', 
            'multipart/x-zip', 
            'application/x-gzip', 
            'multipart/x-gzip');
    }

    public function is_supported_mimetype($mimetype)
    {
        return in_array($mimetype, $this->get_supported_mimetypes());
    }

    public function extract_file($file, $with_safe_names = true)
    {
        $dir = $this->create_temporary_directory();
        $pclzip = new PclZip($file);
        if ($pclzip->extract(PCLZIP_OPT_PATH, $dir) == 0)
        {
            print_r($pclzip->errorInfo());
            
            return false;
        }
        
        if ($with_safe_names)
        {
            Filesystem::create_safe_names($dir);
        }
        
        return $dir;
    }

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
        $pclzip->add($fileList, PCLZIP_OPT_REMOVE_PATH, $path);
        
        return $archiveFile;
    }
}
