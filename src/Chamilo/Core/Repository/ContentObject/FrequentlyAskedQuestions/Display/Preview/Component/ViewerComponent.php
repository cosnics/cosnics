<?php
namespace Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions\Display\Preview\Component;

use Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions\Display\FrequentlyAskedQuestionsDisplaySupport;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\File\Path;

/**
 * Container to enable previews of a portfolio in the context of the repository
 *
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ViewerComponent extends \Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions\Display\Preview\Manager implements
    FrequentlyAskedQuestionsDisplaySupport
{

    function run()
    {
        $factory = new ApplicationFactory(
            \Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions\Display\Manager :: context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        return $factory->run();
    }

    function get_frequently_asked_questions_tree_menu_url()
    {
        return Path :: getInstance()->getBasePath(true) . 'index.php?' . Application :: PARAM_CONTEXT . '=' .
             \Chamilo\Core\Repository\Preview\Manager :: context() . '&' . Application :: PARAM_ACTION . '=' .
             \Chamilo\Core\Repository\Preview\Manager :: ACTION_DISPLAY . '&' .
             \Chamilo\Core\Repository\Preview\Manager :: PARAM_CONTENT_OBJECT_ID . '=' .
             $this->get_root_content_object()->get_id() . '&' .
             \Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions\Display\Manager :: PARAM_STEP . '=%s';
    }

    public function is_allowed_to_set_content_object_rights()
    {
        return true;
    }

    /*
     * (non-PHPdoc) @see
     * \core\repository\content_object\portfolio\display\PortfolioDisplaySupport::get_portfolio_additional_tabs()
     */
    public function get_frequently_asked_questions_additional_tabs()
    {
        return array();
    }

    /**
     *
     * @see \core\repository\content_object\portfolio\display\PortfolioDisplaySupport::is_own_portfolio()
     */
    public function is_own_frequently_asked_questions()
    {
        return $this->get_root_content_object()->get_owner_id() == $this->get_user_id();
    }
}
