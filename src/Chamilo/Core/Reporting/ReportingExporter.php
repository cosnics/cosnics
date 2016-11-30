<?php
namespace Chamilo\Core\Reporting;

use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * $Id: reporting_exporter.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * 
 * @package reporting.lib
 * @author Michael Kyndt
 */
abstract class ReportingExporter
{

    private $template;

    public function __construct($template)
    {
        $this->template = $template;
    }

    public static function factory($type, $template)
    {
        $class = __NAMESPACE__ . '\Exporter\\' . StringUtilities::getInstance()->createString($type)->upperCamelize();
        
        return new $class($template);
    }

    public function get_file_name()
    {
        return $this->get_template()->get_name() . date('_Y-m-d_H-i-s');
    }

    abstract public function export();

    abstract public function save();

    public function get_template()
    {
        return $this->template;
    }

    public function set_template($template)
    {
        $this->template = $template;
    }
}
