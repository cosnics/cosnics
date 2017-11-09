<?php
namespace Chamilo\Core\Repository\External\Renderer;

use Chamilo\Core\Repository\External\ExternalObject;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

abstract class Renderer
{
    const TYPE_TABLE = 'Table';
    const TYPE_GALLERY = 'GalleryTable';
    const TYPE_SLIDESHOW = 'Slideshow';

    protected $external_repository_browser;

    public function __construct($external_repository_browser)
    {
        $this->external_repository_browser = $external_repository_browser;
    }

    public function get_external_repository_browser()
    {
        return $this->external_repository_browser;
    }

    public static function factory($type, $external_repository_browser)
    {
        $class = __NAMESPACE__ . '\Type\\' . StringUtilities::getInstance()->createString($type)->upperCamelize() .
             'Renderer';
        
        if (! class_exists($class))
        {
            throw new Exception(Translation::get('RendererTypeDoesNotExist', array('type' => $type)));
        }
        
        return new $class($external_repository_browser);
    }

    abstract public function as_html();

    public function get_parameters()
    {
        return $this->get_external_repository_browser()->get_parameters();
    }

    public function get_condition()
    {
        return $this->get_external_repository_browser()->get_condition();
    }

    public function count_external_repository_objects($condition)
    {
        return $this->get_external_repository_browser()->count_external_repository_objects($condition);
    }

    public function retrieve_external_repository_objects($condition, $order_property, $offset, $count)
    {
        return $this->get_external_repository_browser()->retrieve_external_repository_objects(
            $condition, 
            $order_property, 
            $offset, 
            $count);
    }

    public function get_external_repository_object_actions(ExternalObject $object)
    {
        return $this->get_external_repository_browser()->get_external_repository_object_actions($object);
    }

    public function is_stand_alone()
    {
        return $this->get_external_repository_browser()->get_parent()->is_stand_alone();
    }

    public function get_url($parameters = array (), $filter = array(), $encode_entities = false)
    {
        return $this->get_external_repository_browser()->get_url($parameters, $filter, $encode_entities);
    }

    /**
     *
     * @param \core\repository\external\ExternalObject $object
     */
    public function get_external_repository_object_viewing_url($object)
    {
        return $this->get_external_repository_browser()->get_external_repository_object_viewing_url($object);
    }
}
