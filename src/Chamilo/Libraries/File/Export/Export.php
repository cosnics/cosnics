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

    /**
     * @var string[]
     */
    private $data;

    /**
     * The filename which will be used for the export file.
     *
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $path;

    /**
     * Constructor
     *
     * @param string[] $data
     */
    public function __construct($data)
    {
        $this->data = $data;
        Export::get_supported_filetypes();
    }

    /**
     * Factory function __construct( create an instance of an export class
     *
     * @param string $type One of the supported file types returned by the get_supported_filetypes function.
     * @param string[] $data
     *
     * @return \Chamilo\Libraries\File\Export\Export
     */
    public static function factory($type, $data)
    {
        $class = __NAMESPACE__ . '\\' . StringUtilities::getInstance()->createString($type)->upperCamelize() . '\\' .
            (string) StringUtilities::getInstance()->createString($type)->upperCamelize() . 'Export';

        return new $class($data);
    }

    /**
     * @return string
     */
    abstract public function getType();

    /**
     * Gets the data
     *
     * @return mixed
     */
    protected function get_data()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function get_filename()
    {
        return $this->filename;
    }

    /**
     * @return string
     */
    public function get_path()
    {
        if ($this->path)
        {
            return $this->path;
        }
        else
        {
            return Path::getInstance()->getArchivePath();
        }
    }

    /**
     * Gets the supported filetypes for export
     *
     * @param string[] $exclude
     *
     * @return string[] Array containig all supported filetypes (keys and values are the same)
     */
    public static function get_supported_filetypes($exclude = [])
    {
        $directories = Filesystem::get_directory_content(__DIR__, Filesystem::LIST_DIRECTORIES, false);
        $types = [];

        foreach ($directories as $index => $directory)
        {
            $type = basename($directory);

            if (!in_array($type, $exclude))
            {
                $types[$type] = $type;
            }
        }

        return $types;
    }

    /**
     * @deprecated Use Export::getType() now
     */
    public function get_type()
    {
        return $this->getType();
    }

    /**
     * @return string
     */
    abstract public function render_data();

    /**
     * Writes the given data to a file and send it to the browser
     */
    public function send_to_browser()
    {
        $file = $this->write_to_file();
        if ($file)
        {
            Filesystem::file_send_for_download($file, true, $this->get_filename());
            exit();
        }
    }

    /**
     * @param string $filename
     */
    public function set_filename($filename)
    {
        $this->filename = $filename . '.' . $this->getType();
    }

    /**
     * @param string $path
     */
    public function set_path($path)
    {
        $this->path = $path;
    }

    /**
     * Writes the given data to a file
     */
    public function write_to_file()
    {
        $file = $this->get_path() . Filesystem::create_unique_name($this->get_path(), $this->get_filename());
        $handle = fopen($file, 'a+');
        if (!fwrite($handle, $this->render_data()))
        {
            return false;
        }
        fclose($handle);

        return $file;
    }
}
