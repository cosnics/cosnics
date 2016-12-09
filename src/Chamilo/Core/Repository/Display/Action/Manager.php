<?php
namespace Chamilo\Core\Repository\Display\Action;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 * $Id: complex_display_component.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @author Michael Kyndt
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    const PARAM_ACTION = \Chamilo\Core\Repository\Display\Manager :: PARAM_ACTION;
    const ATTACHMENT_VIEWER_COMPONENT = 'AttachmentViewer';
    const CONTENT_OBJECT_UPDATER_COMPONENT = 'ContentObjectUpdater';
    const CREATOR_COMPONENT = 'Creator';
    const DELETER_COMPONENT = 'Deleter';
    const REPORTING_TEMPLATE_VIEWER_COMPONENT = 'ReportingTemplateViewer';
    const UPDATER_COMPONENT = 'Updater';
    const DEFAULT_ACTION = \Chamilo\Core\Repository\Display\Manager :: DEFAULT_ACTION;

    public static function factory($type, $application)
    {
        $class = __NAMESPACE__ . '\Component\\' . StringUtilities :: getInstance()->createString($type)->upperCamelize() .
             'Component';
        
        if (! class_exists($class))
        {
            throw new Exception(Translation :: get('ComponentTypeDoesNotExist', array('type' => $type)));
        }
        
        return new $class($application);
    }

    public function get_root_content_object()
    {
        return $this->get_parent()->get_root_content_object();
    }

    public function get_complex_content_object_item()
    {
        return $this->get_parent()->get_complex_content_object_item();
    }

    public function get_selected_complex_content_object_item()
    {
        return $this->get_parent()->get_selected_complex_content_object_item();
    }

    public function get_root_content_object_id()
    {
        return $this->get_parent()->get_root_content_object_id();
    }

    public function get_complex_content_object_item_id()
    {
        return $this->get_parent()->get_complex_content_object_item_id();
    }

    public function get_selected_complex_content_object_item_id()
    {
        return $this->get_parent()->get_selected_complex_content_object_item_id();
    }

    /**
     * Common functionality
     */
    public function get_complex_content_object_table_html()
    {
        return $this->get_parent()->get_complex_content_object_table_html();
    }

    public function get_complex_content_object_table_condition()
    {
        return $this->get_parent()->get_complex_content_object_table_condition();
    }

    public function get_complex_content_object_menu()
    {
        return $this->get_parent()->get_complex_content_object_menu();
    }

    public function get_complex_content_object_breadcrumbs()
    {
        return $this->get_parent()->get_complex_content_object_breadcrumbs();
    }

    public function getButtonToolbarRenderer(ContentObject $content_object)
    {
        return $this->get_parent()->getButtonToolbarRenderer($content_object);
    }

    public function is_allowed($right)
    {
        return $this->get_parent()->is_allowed($right);
    }

    /**
     * Builds the attachment url
     * 
     * @param $attachment ContentObject
     * @param $selected_complex_content_object_item_id int [OPTIONAL] default null
     * @return string
     */
    public function get_content_object_display_attachment_url($attachment, 
        $selected_complex_content_object_item_id = null)
    {
        return $this->get_parent()->get_content_object_display_attachment_url(
            $attachment, 
            $selected_complex_content_object_item_id);
    }
}
