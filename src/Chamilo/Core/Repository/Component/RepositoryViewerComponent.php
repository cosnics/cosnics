<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Platform\Session\Request;

/**
 * $Id: restorer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * 
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component to restore objects.
 * This means moving objects from the recycle bin to there original
 * location.
 */
class RepositoryViewerComponent extends Manager
{
    const PARAM_ELEMENT_NAME = 'element_name';

    public function run()
    {
        $element_name = $this->get_element_name();
        $this->set_parameter(self::PARAM_ELEMENT_NAME, $element_name);
        
        if (! \Chamilo\Core\Repository\Viewer\Manager::is_ready_to_be_published())
        {
            Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);
            
            $factory = new ApplicationFactory(
                \Chamilo\Core\Repository\Viewer\Manager::context(), 
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
            return $factory->run();
        }
        else
        {
            Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);
            
            $html = array();
            
            $html[] = $this->render_header();
            
            $object_id = \Chamilo\Core\Repository\Viewer\Manager::get_selected_objects();
            $object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(File::class_name(), $object_id);
            
            $html[] = '<script type="text/javascript">';
            $html[] = 'window.opener.$("input[name=' . $element_name . '_title]").val("' . addslashes(
                $object->get_title()) . '");';
            $html[] = 'window.opener.$("input[name=' . $element_name . ']").val("' . addslashes($object->get_id()) .
                 '");';
            $html[] = 'window.close();';
            $html[] = '</script>';
            
            $html[] = $this->render_footer();
            
            return implode(PHP_EOL, $html);
        }
    }

    public function get_element_name()
    {
        return Request::get(self::PARAM_ELEMENT_NAME);
    }

    public function get_allowed_content_object_types()
    {
        return array(File::class_name());
    }
}
