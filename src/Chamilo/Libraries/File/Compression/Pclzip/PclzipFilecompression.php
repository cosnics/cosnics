<?php
namespace Chamilo\Libraries\File\Compression\Pclzip;

use Chamilo\Libraries\File\Compression\Filecompression;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
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
            Filesystem :: create_safe_names($dir);
        }

        return $dir;
    }

    public function create_archive($path)
    {
        $archive_file = $this->get_filename();
        $temporary_path = Path :: getInstance()->getTemporaryPath();

        if (! isset($archive_file))
        {
            $archive_file = Filesystem :: create_unique_name($temporary_path, uniqid() . '.zip');
        }

        $archive_file = $temporary_path . uniqid() . '_' . $archive_file;
        $content = Filesystem :: get_directory_content($path, Filesystem :: LIST_FILES, true);

        $pclzip = new PclZip($archive_file);
        $pclzip->add($content, PCLZIP_OPT_REMOVE_PATH, $path);
        return $archive_file;
    }
}
