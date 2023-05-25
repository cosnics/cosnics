<?php
namespace Chamilo\Core\Reporting;

use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\Export\Export;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package reporting.lib
 * @author  Michael Kyndt
 */
abstract class ReportingExporter
{

    private $template;

    public function __construct($template)
    {
        $this->template = $template;
    }

    abstract public function export();

    public static function factory($type, $template)
    {
        $class = __NAMESPACE__ . '\Exporter\\' . StringUtilities::getInstance()->createString($type)->upperCamelize();

        return new $class($template);
    }

    protected function getExporter($fileType): Export
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            'Chamilo\Libraries\File\Export\'' . $fileType . '\'' . $fileType . 'Export'
        );
    }

    public function get_file_name()
    {
        return $this->get_template()->get_name() . date('_Y-m-d_H-i-s');
    }

    public function get_template()
    {
        return $this->template;
    }

    abstract public function save();

    public function set_template($template)
    {
        $this->template = $template;
    }
}
