<?php
namespace Chamilo\Core\Repository\Common\Export;

use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Properties\FileProperties;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package repository.lib
 */
abstract class ContentObjectExportController
{

    private $parameters;

    /**
     *
     * @param ExportParameters $parameters
     */
    public function __construct(ExportParameters $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     *
     * @var ExportParameters
     */
    public function get_parameters()
    {
        return $this->parameters;
    }

    public function set_parameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     *
     * @param $parameters ExportParameters
     */
    public static function factory(ExportParameters $parameters)
    {
        $format = (string) StringUtilities::getInstance()->createString($parameters->get_format())->upperCamelize();
        $class = __NAMESPACE__ . '\\' . $format . '\\' . $format . 'ContentObjectExportController';
        
        return new $class($parameters);
    }

    public function download()
    {
        $path = $this->run();
        
        $file_properties = FileProperties::from_path($path);
        Filesystem::file_send_for_download($path, true, $this->get_filename(), $file_properties->getType());
        Filesystem::remove($path);
    }

    /**
     *
     * @return string
     */
    abstract public function run();

    /**
     *
     * @return string
     */
    abstract public function get_filename();
}
