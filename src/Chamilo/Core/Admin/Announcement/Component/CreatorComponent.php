<?php
namespace Chamilo\Core\Admin\Announcement\Component;

use Chamilo\Core\Admin\Announcement\Manager;
use Chamilo\Core\Admin\Announcement\Publisher;
use Chamilo\Core\Repository\ContentObject\SystemAnnouncement\Storage\DataClass\SystemAnnouncement;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class CreatorComponent extends Manager implements \Chamilo\Core\Repository\Viewer\ViewerInterface
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManageChamilo');

        if (! \Chamilo\Core\Repository\Viewer\Manager::is_ready_to_be_published())
        {
            $factory = new ApplicationFactory(
                \Chamilo\Core\Repository\Viewer\Manager::context(), 
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
            return $factory->run();
        }
        else
        {
            $objects = \Chamilo\Core\Repository\Viewer\Manager::get_selected_objects();
            $publisher = new Publisher($this, $objects);
            
            if ($publisher->ready_to_publish())
            {
                $success = $publisher->publish();
                
                $message = Translation::get(
                    ($success ? 'ObjectPublished' : 'ObjectNotPublished'), 
                    array('OBJECT' => Translation::get('Object')), 
                    Utilities::COMMON_LIBRARIES);
                
                $parameters = array(self::PARAM_ACTION => self::ACTION_BROWSE);
                
                $this->redirect($message, ! $success, $parameters);
            }
            else
            {
                $html = array();
                
                $html[] = $this->render_header();
                $html[] = $publisher->toHtml();
                $html[] = $this->render_footer();
                
                return implode(PHP_EOL, $html);
            }
        }
    }

    public function get_allowed_content_object_types()
    {
        return array(SystemAnnouncement::class_name());
    }

    public function get_additional_parameters()
    {
        return array(
            \Chamilo\Core\Repository\Viewer\Manager::PARAM_ID, 
            \Chamilo\Core\Repository\Viewer\Manager::PARAM_ACTION);
    }
}
