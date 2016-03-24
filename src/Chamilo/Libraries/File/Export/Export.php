<?php
namespace Chamilo\Libraries\File\Export;

use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Abstract class to export tabular data.
 * Create a new type of export by extending this class.
 *
 * @package Chamilo\Libraries\File\Export
 */
abstract class Export
{

    private $data;

    /**
     * The filename which will be used for the export file.
     */
    private $filename;

    private $path;

    /**
     * Constructor
     *
     * @param string $filename
     */
    public function __construct($data)
    {
        $this->data = $data;
        Export :: get_supported_filetypes();
    }

    /**
     * Gets the data
     *
     * @return string
     */
    protected function get_data()
    {
        return $this->data;
    }

    public function get_filename()
    {
        return $this->filename;
    }

    public function set_filename($filename)
    {
        $this->filename = $filename . '.' . $this->get_type();
    }

    public function get_path()
    {
        if ($this->path)
        {
            return $this->path;
        }
        else
        {
            return Path :: getInstance()->getArchivePath();
        }
    }

    public function set_path($path)
    {
        $this->path = $path;
    }

    abstract public function get_type();

    /**
     * Writes the given data to a file
     *
     * @param array $data
     */
    public function write_to_file()
    {
        $file = $this->get_path() . Filesystem :: create_unique_name($this->get_path(), $this->get_filename());
        $handle = fopen($file, 'a+');
        if (! fwrite($handle, $this->render_data()))
        {
            return false;
        }
        fclose($handle);
        return $file;
    }

    public function send_to_browser()
    {
        $file = $this->write_to_file();
        if ($file)
        {
            Filesystem :: file_send_for_download($file, true, $this->get_filename());
            exit();
        }
    }

    abstract public function render_data();

    /**
     * Gets the supported filetypes for export
     *
     * @return array Array containig all supported filetypes (keys and values are the same)
     */
    public static function get_supported_filetypes($exclude = array())
    {
        $directories = Filesystem :: get_directory_content(__DIR__, Filesystem :: LIST_DIRECTORIES, false);
        foreach ($directories as $index => $directory)
        {
            $type = basename($directory);

            if (! in_array($type, $exclude))
            {
                $types[$type] = $type;
            }
        }
        return $types;
    }

    /**
     * Factory function __construct( create an instance of an export class
     *
     * @param string $type One of the supported file types returned by the get_supported_filetypes function.
     * @param string $filename The desired filename for the export file (extension will be automatically added depending
     *        on the given $type)
     * @return \Chamilo\Libraries\File\Export
     */
    public static function factory($type, $data)
    {
        $class = __NAMESPACE__ . '\\' . StringUtilities :: getInstance()->createString($type)->upperCamelize() . '\\' .
             (string) StringUtilities :: getInstance()->createString($type)->upperCamelize() . 'Export';

        if (class_exists($class))
        {
            return new $class($data);
        }
    }
}
