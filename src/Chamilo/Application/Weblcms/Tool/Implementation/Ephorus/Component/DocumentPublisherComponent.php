<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Manager;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Translation;

/**
 * This class sends a request for a document to ephorus
 * 
 * @author Tom Goethals - Hogeschool Gent
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DocumentPublisherComponent extends Manager implements \Chamilo\Core\Repository\Viewer\ViewerInterface
{

    /**
     * Runs this component
     */
    public function run()
    {
        if (! $this->is_allowed(WeblcmsRights::ADD_RIGHT))
        {
            throw new NotAllowedException();
        }
        
        if (! \Chamilo\Core\Repository\Viewer\Manager::is_ready_to_be_published())
        {
            $factory = new ApplicationFactory(
                \Chamilo\Core\Repository\Viewer\Manager::context(), 
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
            $component = $factory->getComponent();
            $component->set_maximum_select(\Chamilo\Core\Repository\Viewer\Manager::SELECT_SINGLE);
            return $component->run();
        }
        else
        {
            $objects = \Chamilo\Core\Repository\Viewer\Manager::get_selected_objects();
            
            $parameters = array(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_EPHORUS_REQUEST, 
                \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Manager::ACTION_CREATE, 
                Manager::PARAM_CONTENT_OBJECT_IDS => $objects);
            
            $this->redirect('', false, $parameters);
        }
    }

    /**
     * Returns the allowed content object types for the repoviewer
     * 
     * @return string[]
     */
    public function get_allowed_content_object_types()
    {
        return array(File::class_name());
    }

    public function render_header()
    {
        $html = array();
        
        $html[] = parent::render_header();
        $html[] = $this->display_warning_message(Translation::get("EphorusMaxUploadSize"));
        
        return implode(PHP_EOL, $html);
    }
}
