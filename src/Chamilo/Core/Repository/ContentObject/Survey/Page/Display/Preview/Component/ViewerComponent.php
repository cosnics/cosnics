<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Preview\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Interfaces\PageDisplaySupport;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Path;

/**
 *
 * @package repository.content_object.survey_page
 * @author Eduard Vossen
 * @author Magali Gillard
 */
class ViewerComponent extends \Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Preview\Manager implements
    PageDisplaySupport
{
    const TEMPORARY_STORAGE = 'survey_page_preview';

    function run()
    {
        $factory = new ApplicationFactory(
            $this->getRequest(),
            \Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Manager :: context(),
            $this->get_user(),
            $this);
        return $factory->run();
    }

    function get_answer($complex_question_id)
    {
        $answers = Session :: retrieve(self :: TEMPORARY_STORAGE);

        if ($answers)
        {
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
        else
        {
            return null;
        }
    }
  
    function get_page_tree_menu_url()
    {
        return Path :: getInstance()->getBasePath(true) . 'index.php?' . Application :: PARAM_CONTEXT . '=' .
            \Chamilo\Core\Repository\Preview\Manager :: context() . '&' . Application :: PARAM_ACTION . '=' .
            \Chamilo\Core\Repository\Preview\Manager :: ACTION_DISPLAY . '&' .
            \Chamilo\Core\Repository\Preview\Manager :: PARAM_CONTENT_OBJECT_ID . '=' .
            $this->get_root_content_object()->get_id() . '&' .
            \Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Manager :: PARAM_STEP . '=%s';
    }
    
  
    public function get_page_additional_tabs()
    {
        return array();
    }
    
    /**
     *
     * @see \core\repository\content_object\page\display\PortfolioDisplaySupport::is_own_page()
     */
    public function is_own_page()
    {
        return $this->get_root_content_object()->get_owner_id() == $this->get_user_id();
    }
    
}
?>