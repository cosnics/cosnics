<?php
namespace Chamilo\Application\Survey\Export\Component;

use Chamilo\Libraries\Utilities\Utilities;

abstract class Exporter
{

    private $export_template;

    private $publication_id;

    public function __construct($export_template, $publication_id)
    {
        $this->export_template = $export_template;
        $this->publication_id = $publication_id;
    }

    public static function factory($export_template, $publication_id)
    {
        $class = __NAMESPACE__ . '\\' . Utilities :: underscores_to_camelcase($export_template->get_type());
        
        return new $class($export_template, $publication_id);
    }

    function get_file_name()
    {
        return $this->get_export_template()->get_name() . date('_Y-m-d_H-i-s');
    }

    abstract function save();

    function get_export_template()
    {
        return $this->export_template;
    }

    function set_export_template($export_template)
    {
        $this->export_template = $export_template;
    }

    function get_publication_id()
    {
        return $this->publication_id;
    }

    function set_publication_id($publication_id)
    {
        $this->publication_id = $publication_id;
    }
}
?>