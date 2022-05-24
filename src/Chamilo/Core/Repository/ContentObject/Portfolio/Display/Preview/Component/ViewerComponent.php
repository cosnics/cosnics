<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Preview\Component;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioDisplaySupport;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Preview\DummyFeedback;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Preview\DummyNotification;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Preview\PreviewStorage;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\File\Path;
use Exception;

/**
 * Container to enable previews of a portfolio in the context of the repository
 *
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ViewerComponent extends \Chamilo\Core\Repository\ContentObject\Portfolio\Display\Preview\Manager
    implements PortfolioDisplaySupport
{

    function run()
    {
        return $this->getApplicationFactory()->getApplication(
            Manager::context(), new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
        )->run();
    }

    /**
     *
     * @see \core\repository\content_object\portfolio\display\PortfolioDisplaySupport::count_portfolio_feedbacks()
     */
    function count_portfolio_feedbacks(ComplexContentObjectPathNode $node)
    {
        return $this->retrieve_portfolio_feedbacks($node)->count();
    }

    public function get_portfolio_additional_actions()
    {
        return [];
    }

    /**
     *
     * @see \core\repository\content_object\portfolio\display\PortfolioDisplaySupport::get_portfolio_feedback()
     */
    public function get_portfolio_feedback()
    {
        $feedback = new DummyFeedback();
        $feedback->set_content_object_id($this->get_root_content_object()->get_id());

        return $feedback;
    }

    /**
     *
     * @see \core\repository\content_object\portfolio\display\PortfolioDisplaySupport::get_portfolio_notification()
     */
    public function get_portfolio_notification()
    {
        $notification = new DummyNotification();
        $notification->set_content_object_id($this->get_root_content_object()->get_id());

        return $notification;
    }

    /**
     *
     * @see \core\repository\content_object\portfolio\display\PortfolioDisplaySupport::get_portfolio_tree_menu_url()
     */
    function get_portfolio_tree_menu_url()
    {
        return Path::getInstance()->getBasePath(true) . 'index.php?' . Application::PARAM_CONTEXT . '=' .
            \Chamilo\Core\Repository\Preview\Manager::context() . '&' . Application::PARAM_ACTION . '=' .
            \Chamilo\Core\Repository\Preview\Manager::ACTION_DISPLAY . '&' .
            \Chamilo\Core\Repository\Preview\Manager::PARAM_CONTENT_OBJECT_ID . '=' .
            $this->get_root_content_object()->get_id() . '&' . Manager::PARAM_STEP . '=%s';
    }

    /*
     * (non-PHPdoc) @see
     * \core\repository\content_object\portfolio\PortfolioDisplaySupport::is_allowed_to_update_feedback()
     */

    public function is_allowed_to_create_feedback(ComplexContentObjectPathNode $node)
    {
        return true;
    }

    /*
     * (non-PHPdoc) @see
     * \core\repository\content_object\portfolio\PortfolioDisplaySupport::is_allowed_to_view_feedback()
     */

    public function is_allowed_to_set_content_object_rights()
    {
        return true;
    }

    /*
     * (non-PHPdoc) @see
     * \core\repository\content_object\portfolio\PortfolioDisplaySupport::is_allowed_to_create_feedback()
     */

    public function is_allowed_to_update_feedback($feedback)
    {
        return true;
    }

    public function is_allowed_to_view_feedback(ComplexContentObjectPathNode $node)
    {
        return true;
    }

    /*
     * (non-PHPdoc) @see
     * \core\repository\content_object\portfolio\display\PortfolioDisplaySupport::get_portfolio_additional_actions()
     */

    /**
     *
     * @see \core\repository\content_object\portfolio\display\PortfolioDisplaySupport::is_own_portfolio()
     */
    public function is_own_portfolio()
    {
        return $this->get_root_content_object()->get_owner_id() == $this->get_user_id();
    }

    /**
     *
     * @param ComplexContentObjectPathNode $node
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassCollection
     */
    public function retrievePortfolioNotifications(ComplexContentObjectPathNode $node)
    {
        return PreviewStorage::getInstance()->retrieve_notifications(
            $this->get_root_content_object()->get_id(),
            $node->get_complex_content_object_item() ? $node->get_complex_content_object_item()->get_id() : 0
        );
    }

    /**
     *
     * @see \core\repository\content_object\portfolio\display\PortfolioDisplaySupport::retrieve_portfolio_feedback()
     */
    public function retrieve_portfolio_feedback($feedback_id)
    {
        try
        {
            return PreviewStorage::getInstance()->retrieve_feedback($feedback_id);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode $node
     * @param int $count
     * @param int $offset
     *
     * @return \ArrayIterator
     * @see \core\repository\content_object\portfolio\display\PortfolioDisplaySupport::retrieve_portfolio_feedbacks()
     *
     */
    function retrieve_portfolio_feedbacks(ComplexContentObjectPathNode $node, $count = null, $offset = null)
    {
        return PreviewStorage::getInstance()->retrieve_feedbacks(
            $this->get_root_content_object()->get_id(),
            $node->get_complex_content_object_item() ? $node->get_complex_content_object_item()->get_id() : 0
        );
    }

    /**
     *
     * @see \core\repository\content_object\portfolio\display\PortfolioDisplaySupport::retrieve_portfolio_notification()
     */
    function retrieve_portfolio_notification(ComplexContentObjectPathNode $node)
    {
        return PreviewStorage::getInstance()->retrieve_notification(
            $this->get_root_content_object()->get_id(),
            $node->get_complex_content_object_item() ? $node->get_complex_content_object_item()->get_id() : 0
        );
    }
}
