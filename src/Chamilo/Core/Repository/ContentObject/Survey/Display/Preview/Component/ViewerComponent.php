<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Display\Preview\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Display\Interfaces\SurveyDisplaySupport;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Architecture\Application\Application;

class ViewerComponent extends \Chamilo\Core\Repository\ContentObject\Survey\Display\Preview\Manager implements
    SurveyDisplaySupport
{
    const TEMPORARY_STORAGE = 'survey_preview';
    const PARAM_COMPLEX_QUESTION_ID = 'complex_question_id';

    function run()
    {
        $factory = new ApplicationFactory(
            \Chamilo\Core\Repository\ContentObject\Survey\Display\Manager :: context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        return $factory->run();
    }

    public function get_answer($complex_question_id)
    {
        $answers = Session :: retrieve(self :: TEMPORARY_STORAGE);

        $answer = $answers[$complex_question_id];
        
        if ($answer)
        {
            return $answer;
        }
        else
        {
            return null;
        }
    }

    function save_answer($complex_question_id, $answer)
    {
        if (! Session :: retrieve(self :: TEMPORARY_STORAGE))
        {
            Session :: register(self :: TEMPORARY_STORAGE, array());
        }

        $answers = Session :: retrieve(self :: TEMPORARY_STORAGE);
      
        
        $answers[$complex_question_id] = $answer;
        Session :: register(self :: TEMPORARY_STORAGE, $answers);
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