<?php
namespace Chamilo\Core\Repository\Processor;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Core\Repository\Processor
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class HtmlEditorProcessor
{

    private $selected_content_objects;

    private $parent;

    public static function factory($type, $parent, $selected_content_objects)
    {
        $editor = LocalSetting::getInstance()->get('html_editor');
        $class = __NAMESPACE__ . '\\' . StringUtilities::getInstance()->createString($editor)->upperCamelize() .
             '\Processor';
        
        if (class_exists($class))
        {
            return new $class($parent, $selected_content_objects);
        }
    }

    public function __construct($parent, $selected_content_objects)
    {
        $this->set_parent($parent);
        $this->set_selected_content_objects($selected_content_objects);
    }

    public function get_selected_content_objects()
    {
        return $this->selected_content_objects;
    }

    public function set_selected_content_objects($selected_content_objects)
    {
        $this->selected_content_objects = $selected_content_objects;
    }

    public function get_parent()
    {
        return $this->parent;
    }

    public function set_parent($parent)
    {
        $this->parent = $parent;
    }

    public function get_parameters()
    {
        return $this->get_parent()->get_parameters();
    }

    public function get_parameter($key)
    {
        return $this->get_parent()->get_parameter($key);
    }

    public function get_repository_document_display_url($parameters = array (), $filter = [], $encode_entities = false)
    {
        $parameters = array_merge(
            array(Manager::PARAM_ACTION => Manager::ACTION_DOWNLOAD_DOCUMENT, 'display' => 1), 
            $parameters);
        
        $redirect = new Redirect($parameters, $filter, $encode_entities);
        
        return $redirect->getUrl();
    }

    public function get_repository_document_display_matching_url()
    {
        $matching_url = self::get_repository_document_display_url(
            array(Manager::PARAM_CONTENT_OBJECT_ID => '', ContentObject::PARAM_SECURITY_CODE => ''));
        $matching_url = preg_quote($matching_url);
        
        $original_object_string = '&' . Manager::PARAM_CONTENT_OBJECT_ID . '\=';
        $replace_object_string = '&' . Manager::PARAM_CONTENT_OBJECT_ID . '\=[0-9]+';
        
        $matching_url = str_replace($original_object_string, $replace_object_string, $matching_url);
        
        $original_object_string = '&' . ContentObject::PARAM_SECURITY_CODE . '\=';
        $replace_object_string = '(&' . ContentObject::PARAM_SECURITY_CODE . '\=[^\&]+)?';
        
        $matching_url = str_replace($original_object_string, $replace_object_string, $matching_url);
        
        return '/' . $matching_url . '/';
    }

    abstract public function run();
}
