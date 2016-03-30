<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Display\Preview\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Configuration\SurveyConfiguration;
use Chamilo\Core\Repository\ContentObject\Survey\Display\Interfaces\SurveyDisplaySupport;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\File\Path;

class ViewerComponent extends \Chamilo\Core\Repository\ContentObject\Survey\Display\Preview\Manager implements 
    SurveyDisplaySupport
{

    function run()
    {
        $surveyConfiguration = new SurveyConfiguration(
            $this->getRequest(), 
            $this->get_user(), 
            $this, '\Chamilo\Core\Repository\ContentObject\Survey');
        $factory = new ApplicationFactory(
            \Chamilo\Core\Repository\ContentObject\Survey\Display\Manager :: context(), 
            $surveyConfiguration);
        return $factory->run();
    }

    function get_tree_menu_url()
    {
        return Path :: getInstance()->getBasePath(true) . 'index.php?' . Application :: PARAM_CONTEXT . '=' .
             \Chamilo\Core\Repository\Preview\Manager :: context() . '&' . Application :: PARAM_ACTION . '=' .
             \Chamilo\Core\Repository\Preview\Manager :: ACTION_DISPLAY . '&' .
             \Chamilo\Core\Repository\Preview\Manager :: PARAM_CONTENT_OBJECT_ID . '=' .
             $this->get_root_content_object()->get_id() . '&' .
             \Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Manager :: PARAM_STEP . '=%s';
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
}
?>