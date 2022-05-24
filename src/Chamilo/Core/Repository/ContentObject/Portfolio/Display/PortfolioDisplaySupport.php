<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Notification;

/**
 * Interface which indicates a component implements a repository\content_object\portfolio\display
 *
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @todo The \core\repository\content_object\portfolio\Support requirements partially conflict (in definition) with the
 *       the
 *       existing DisplaySupport, therefore the DisplaySupport has been temporarily disabled pending the resolving of
 *       these feedback issues. Methods copied from DisplaySupport are marked as such with a todo-tag
 */
interface PortfolioDisplaySupport
{

    /**
     * Count the number of Feedback objects
     *
     * @param \core\repository\common\path\ComplexContentObjectPathNode $node
     *
     * @return int
     */
    public function count_portfolio_feedbacks(ComplexContentObjectPathNode $node);

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[]
     */
    public function get_portfolio_additional_actions();

    /**
     * Returns an newly instantiated Feedback object
     *
     * @return \core\repository\content_object\portfolio\Feedback
     */
    public function get_portfolio_feedback();

    /**
     * Returns an newly instantiated Notification object
     *
     * @return \core\repository\content_object\portfolio\Notification
     */
    public function get_portfolio_notification();

    /**
     * Return a URI-template for the portfolio tree menu
     *
     * @return string
     */
    public function get_portfolio_tree_menu_url();

    /**
     * Determine the complex content object that should be displayed
     *
     * @return ContentObject
     * @todo Fix DisplaySupport conflicts
     */
    public function get_root_content_object();

    /**
     * Is the user allowed to create feedback for the given node
     *
     * @param ComplexContentObjectPathNode $node
     *
     * @return boolean
     */
    public function is_allowed_to_create_feedback(ComplexContentObjectPathNode $node);

    /**
     * Is the user allowed to delete this feedback instance
     *
     * @param Feedback $feedback
     *
     * @return boolean
     * @todo Fix DisplaySupport conflicts, footprint changed
     */
    public function is_allowed_to_delete_feedback($feedback);

    /**
     * Is the user allowed to edit the content for the given node
     *
     * @param ComplexContentObjectPathNode $node
     *
     * @return boolean
     */
    public function is_allowed_to_edit_content_object(ComplexContentObjectPathNode $node);

    /**
     * Is the user allowed to update this feedback instance
     *
     * @param Feedback $feedback
     *
     * @return boolean
     * @todo Fix DisplaySupport conflicts, footprint changed
     */
    public function is_allowed_to_update_feedback($feedback);

    /**
     * Is the user allowed to view the content for the given node
     *
     * @param ComplexContentObjectPathNode $node
     *
     * @return boolean
     */
    public function is_allowed_to_view_content_object(ComplexContentObjectPathNode $node);

    /**
     * Is the user allowed to view feedback for the given node
     *
     * @param ComplexContentObjectPathNode $node
     *
     * @return boolean
     */
    public function is_allowed_to_view_feedback(ComplexContentObjectPathNode $node);

    /**
     * Determine whether the portfolio being displayed is the user's own portfolio
     *
     * @return boolean
     */
    public function is_own_portfolio();

    /**
     * Retrieves the notifications for the current node
     *
     * @param ComplexContentObjectPathNode $node
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassCollection
     */
    public function retrievePortfolioNotifications(ComplexContentObjectPathNode $node);

    /**
     * Retrieve a specific Feedback instance
     *
     * @param int $feedback_id
     *
     * @return \core\repository\content_object\portfolio\Feedback
     */
    public function retrieve_portfolio_feedback($feedback_id);

    /**
     * Retrieve a DataClassCollection of Feedback objects
     *
     * @param \core\repository\common\path\ComplexContentObjectPathNode $node
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassCollection
     */
    public function retrieve_portfolio_feedbacks(ComplexContentObjectPathNode $node, $count, $offset);

    /**
     * Retrieve a Notification
     *
     * @param ComplexContentObjectPathNode $node
     *
     * @return Notification
     */
    public function retrieve_portfolio_notification(ComplexContentObjectPathNode $node);
}
