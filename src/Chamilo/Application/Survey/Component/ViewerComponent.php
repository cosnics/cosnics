<?php
namespace Chamilo\Application\Survey\Component;

use Chamilo\Application\Survey\Manager;
use Chamilo\Core\Repository\ContentObject\Survey\Display\Interfaces\SurveyDisplaySupport;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Core\Repository\ContentObject\Survey\Configuration\SurveyConfiguration;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Path;
use Chamilo\Application\Survey\Storage\DataManager;
use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Format\Theme;

class ViewerComponent extends Manager implements DelegateComponent, SurveyDisplaySupport
{

    private $contentObject;

    private $publicationId;

    private $publication;

    function run()
    {
        $this->publicationId = $this->getApplicationConfiguration()->getRequest()->get(self :: PARAM_PUBLICATION_ID);
             
        $this->publication = DataManager :: retrieve_by_id(Publication :: class_name(), $this->publicationId);
        
        $this->contentObject = $this->publication->getContentObject();
        
        $surveyConfiguration = new SurveyConfiguration(
            $this->getRequest(), 
            $this->get_user(), 
            $this, 
            '\Chamilo\Application\Survey');
        $factory = new ApplicationFactory(
            \Chamilo\Core\Repository\ContentObject\Survey\Display\Manager :: context(), 
            $surveyConfiguration);
        return $factory->run();
    }

    function get_tree_menu_url()
    {
        return Path :: getInstance()->getBasePath(true) . 'index.php?' . Application :: PARAM_CONTEXT . '=' .
             \Chamilo\Application\Survey\Manager :: context() . '&' . Application :: PARAM_ACTION . '=' .
             \Chamilo\Application\Survey\Manager :: ACTION_VIEW . '&' .
             \Chamilo\Application\Survey\Manager :: PARAM_PUBLICATION_ID . '=' .
             $this->publicationId . '&' .
             \Chamilo\Core\Repository\ContentObject\Survey\Display\Manager :: PARAM_STEP . '=%s';
    }

    public function get_additional_tabs()
    {
        return array();
    }

    /**
     *
     * @see \core\repository\content_object\page\display\PortfolioDisplaySupport::is_own_page()
     */
    public function is_own_survey()
    {
        return $this->get_root_content_object()->get_owner_id() == $this->get_user_id();
    }

    public function get_root_content_object()
    {
        return $this->contentObject;
    }

    /**
     *
     * @param int $right
     * @return boolean
     */
    public function is_allowed($right)
    {
        return true;
    }

    /**
     *
     * @return boolean
     */
    public function is_allowed_to_add_child()
    {
        return false;
    }

    /**
     *
     * @return boolean
     */
    public function is_allowed_to_delete_child()
    {
        return false;
    }

    /**
     *
     * @return boolean
     */
    public function is_allowed_to_delete_feedback()
    {
        return false;
    }

    /**
     *
     * @return boolean
     */
    public function is_allowed_to_edit_content_object()
    {
        return false;
    }

    /**
     *
     * @return boolean
     */
    public function is_allowed_to_edit_feedback()
    {
        return false;
    }

    /**
     *
     * @return boolean
     */
    public function is_allowed_to_view_content_object()
    {
        return true;
    }
    
    public function get_additional_parameters()
    {
        return array(self :: PARAM_PUBLICATION_ID);
    }
}

?>