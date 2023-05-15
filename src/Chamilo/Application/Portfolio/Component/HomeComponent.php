<?php
namespace Chamilo\Application\Portfolio\Component;

use Chamilo\Application\Portfolio\Manager;
use Chamilo\Application\Portfolio\Storage\DataClass\Publication;
use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\Bookmark\Storage\DataClass\Bookmark;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Manager as PortfolioDisplayManager;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Menu;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioBookmarkSupport;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioComplexRights;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioDisplaySupport;
use Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Portfolio;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;

/**
 *
 * @package Chamilo\Application\Portfolio\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class HomeComponent extends Manager
    implements PortfolioDisplaySupport, DelegateComponent, PortfolioComplexRights, PortfolioBookmarkSupport
{

    /**
     *
     * @var \Chamilo\Application\Portfolio\Storage\DataClass\Publication
     */
    private $publication;

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::run()
     */
    public function run()
    {
        $this->set_parameter(self::PARAM_USER_ID, $this->getCurrentUserId());

        return $this->getApplicationFactory()->getApplication(
            Portfolio::CONTEXT . '\Display',
            new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this)
        )->run();
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioComplexRights::clear_virtual_user_id()
     */
    public function clear_virtual_user_id()
    {
        return $this->getRightsService()->clearVirtualUser();
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioDisplaySupport::count_portfolio_feedbacks()
     */
    public function count_portfolio_feedbacks(ComplexContentObjectPathNode $node)
    {
        return $this->getFeedbackService()->countFeedbackForPublicationNodeAndUser(
            $this->getPublication(), $node, $this->getUser()
        );
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioComplexRights::count_portfolio_possible_view_users()
     */
    public function count_portfolio_possible_view_users($condition)
    {
        return $this->getUserService()->countUsers($condition);
    }

    /**
     *
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\Publication
     */
    public function getPublication()
    {
        if (!isset($this->publication))
        {
            $this->publication = $this->getPublicationService()->findPublicationForUserIdentifier(
                $this->getCurrentUserId()
            );

            if (!$this->publication instanceof Publication)
            {
                $this->publication = $this->getPublicationService()->createRootPortfolioAndPublicationForUser(
                    $this->getCurrentUser()
                );
            }
        }

        return $this->publication;
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioComplexRights::get_available_rights()
     */
    public function get_available_rights()
    {
        return $this->getRightsService()->getAvailableRights();
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioComplexRights::get_entities()
     */
    public function get_entities()
    {
        return array(
            UserEntity::ENTITY_TYPE => new UserEntity(), PlatformGroupEntity::ENTITY_TYPE => new PlatformGroupEntity()
        );
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioComplexRights::get_locations()
     */
    public function get_locations($nodes)
    {
        $locations = [];

        foreach ($nodes as $node)
        {
            $locations[] = $this->getRightsService()->get_location($node, $this->getPublication()->get_id());
        }

        return $locations;
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioDisplaySupport::get_portfolio_additional_actions()
     */
    public function get_portfolio_additional_actions()
    {
        return array(
            new Button(
                $this->getTranslator()->trans('BrowserComponent', [], Manager::CONTEXT),
                new FontAwesomeGlyph('search'), $this->get_url(
                array(self::PARAM_ACTION => self::ACTION_BROWSE), array(
                    self::PARAM_USER_ID, PortfolioDisplayManager::PARAM_ACTION, PortfolioDisplayManager::PARAM_STEP
                )
            )
            )
        );
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioBookmarkSupport::get_portfolio_bookmark()
     */
    public function get_portfolio_bookmark($current_step)
    {
        $portfolioOwner = $this->get_root_content_object()->get_owner();

        $content_object = new Bookmark();
        $content_object->set_title(
            $this->getTranslator()->trans(
                'BookmarkTitle', ['NAME' => $portfolioOwner->get_fullname()], Manager::CONTEXT
            )
        );
        $content_object->set_description(
            $this->getTranslator()->trans(
                'BookmarkDescription', ['NAME' => $portfolioOwner->get_fullname()], Manager::CONTEXT
            )
        );
        $content_object->set_application(__NAMESPACE__);
        $content_object->set_url(
            $this->get_url(
                array(
                    PortfolioDisplayManager::PARAM_ACTION => PortfolioDisplayManager::ACTION_VIEW_COMPLEX_CONTENT_OBJECT,
                    PortfolioDisplayManager::PARAM_STEP => $current_step
                )
            )
        );
        $content_object->set_owner_id($this->get_user_id());

        return $content_object;
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioDisplaySupport::get_portfolio_feedback()
     */
    public function get_portfolio_feedback()
    {
        return $this->getFeedbackService()->getFeedbackInstanceForPublication($this->getPublication());
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioDisplaySupport::get_portfolio_notification()
     */
    public function get_portfolio_notification()
    {
        return $this->getNotificationService()->getNotificationInstanceForPublication($this->getPublication());
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioDisplaySupport::get_portfolio_tree_menu_url()
     */
    public function get_portfolio_tree_menu_url()
    {
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => Manager::CONTEXT, Application::PARAM_ACTION => Manager::ACTION_HOME,
                Manager::PARAM_USER_ID => $this->getCurrentUserId(),
                PortfolioDisplayManager::PARAM_ACTION => PortfolioDisplayManager::ACTION_VIEW_COMPLEX_CONTENT_OBJECT,
                PortfolioDisplayManager::PARAM_STEP => Menu::NODE_PLACEHOLDER
            )
        );

        return $redirect->getUrl();
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioComplexRights::get_portfolio_virtual_user()
     */
    public function get_portfolio_virtual_user()
    {
        return $this->getRightsService()->getVirtualUser();
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioDisplaySupport::get_root_content_object()
     */
    public function get_root_content_object()
    {
        return $this->getPublication()->get_content_object();
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioComplexRights::get_selected_entities()
     */
    public function get_selected_entities(ComplexContentObjectPathNode $node)
    {
        return $this->getRightsService()->findRightsLocationEntityRightsForPublicationNodeAndAvailableRights(
            $this->getPublication(), $node
        );
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioComplexRights::invert_location_entity_right()
     */
    public function invert_location_entity_right($rightId, $entityId, $entityType, $locationId)
    {
        return $this->getRightsService()->invertLocationEntityRight(
            $rightId, $entityId, $entityType, $locationId, $this->getPublication()->getId()
        );
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioDisplaySupport::is_allowed_to_create_feedback()
     */
    public function is_allowed_to_create_feedback(ComplexContentObjectPathNode $node = null)
    {
        return $this->getRightsService()->isAllowedToCreateFeedback($this->getPublication(), $this->getUser(), $node);
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioDisplaySupport::is_allowed_to_delete_feedback()
     */
    public function is_allowed_to_delete_feedback($feedback)
    {
        return $this->getRightsService()->isFeedbackOwner($feedback, $this->getUser());
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioDisplaySupport::is_allowed_to_edit_content_object()
     */
    public function is_allowed_to_edit_content_object(ComplexContentObjectPathNode $node = null)
    {
        return $this->getRightsService()->isAllowedToEditContentObject(
            $this->getPublication(), $this->getUser(), $node
        );
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioComplexRights::is_allowed_to_set_content_object_rights()
     */
    public function is_allowed_to_set_content_object_rights()
    {
        return $this->getRightsService()->isAllowedToSetContentObjectRights($this->getUser(), $this->getPublication());
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioDisplaySupport::is_allowed_to_update_feedback()
     */
    public function is_allowed_to_update_feedback($feedback)
    {
        return $this->getRightsService()->isFeedbackOwner($feedback, $this->getUser());
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioDisplaySupport::is_allowed_to_view_content_object()
     */
    public function is_allowed_to_view_content_object(ComplexContentObjectPathNode $node = null)
    {
        return $this->getRightsService()->isAllowedToViewContentObjectForNode(
            $this->getPublication(), $this->getUser(), $node
        );
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioDisplaySupport::is_allowed_to_view_feedback()
     */
    public function is_allowed_to_view_feedback(ComplexContentObjectPathNode $node = null)
    {
        return $this->getRightsService()->isAllowedToViewFeedback($this->getPublication(), $this->getUser(), $node);
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioDisplaySupport::is_own_portfolio()
     */
    public function is_own_portfolio()
    {
        return $this->getRightsService()->isPublisher($this->getPublication(), $this->getUser());
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioDisplaySupport::retrievePortfolioNotifications()
     */
    public function retrievePortfolioNotifications(
        ComplexContentObjectPathNode $node
    )
    {
        return $this->getNotificationService()->findPortfolioNotificationsForPublicationAndNode(
            $this->getPublication(), $node
        );
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioDisplaySupport::retrieve_portfolio_feedback()
     */
    public function retrieve_portfolio_feedback($feedbackIdentifier)
    {
        return $this->getFeedbackService()->findFeedbackByIdentfier($feedbackIdentifier);
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioDisplaySupport::retrieve_portfolio_feedbacks()
     */
    public function retrieve_portfolio_feedbacks(ComplexContentObjectPathNode $node, $count, $offset)
    {
        return $this->getFeedbackService()->findFeedbackForPublicationNodeUserIdentifierCountAndOffset(
            $this->getPublication(), $node, $this->getUser(), $count, $offset
        );
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioDisplaySupport::retrieve_portfolio_notification()
     */
    public function retrieve_portfolio_notification(
        ComplexContentObjectPathNode $node
    )
    {
        return $this->getNotificationService()->findPortfolioNotificationForPublicationUserAndNode(
            $this->getPublication(), $this->getUser(), $node
        );
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioComplexRights::retrieve_portfolio_possible_view_users()
     */
    public function retrieve_portfolio_possible_view_users($condition, $count, $offset, $orderProperty)
    {
        return $this->getUserService()->findUsers($condition, $offset, $count, $orderProperty);
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioComplexRights::set_portfolio_virtual_user_id()
     */
    public function set_portfolio_virtual_user_id($virtualUserIdentifier)
    {
        return $this->getRightsService()->setVirtualUser($virtualUserIdentifier);
    }
}