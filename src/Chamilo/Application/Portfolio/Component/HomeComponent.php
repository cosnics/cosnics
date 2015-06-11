<?php
namespace Chamilo\Application\Portfolio\Component;

use Chamilo\Application\Portfolio\Manager;
use Chamilo\Application\Portfolio\Rights;
use Chamilo\Application\Portfolio\Storage\DataClass\Feedback;
use Chamilo\Application\Portfolio\Storage\DataClass\Publication;
use Chamilo\Application\Portfolio\Storage\DataClass\Notification;
use Chamilo\Application\Portfolio\Storage\DataManager;
use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\Bookmark\Storage\DataClass\Bookmark;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioBookmarkSupport;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioComplexRights;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioDisplaySupport;
use Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Portfolio;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Core\Repository\Workspace\Service\RightsService;

/**
 * Main portfolio viewing component
 *
 * @package application\portfolio$HomeComponent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class HomeComponent extends \Chamilo\Application\Portfolio\Manager implements PortfolioDisplaySupport, DelegateComponent,
    PortfolioComplexRights, PortfolioBookmarkSupport
{

    /**
     *
     * @var \application\portfolio\Publication
     */
    private $publication;

    /**
     *
     * @var \user\User
     */
    private $virtual_user;

    /**
     *
     * @var int
     */
    private $rights_user_id;

    public function run()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication :: class_name(), Publication :: PROPERTY_PUBLISHER_ID),
            new StaticConditionVariable($this->get_current_user_id()));
        $this->publication = DataManager :: retrieve(
            Publication :: class_name(),
            new DataClassRetrieveParameters($condition));

        if (! $this->publication instanceof Publication && $this->get_current_user_id() == $this->get_user_id())
        {
            $template_registration = \Chamilo\Core\Repository\Configuration :: registration_default_by_type(
                Portfolio :: package());

            $portfolio = new Portfolio();
            $portfolio->set_title($this->get_user()->get_fullname());
            $portfolio->set_description(Translation :: get('NoInstructionYetDescription'));
            $portfolio->set_owner_id($this->get_user_id());

            $portfolio->set_template_registration_id($template_registration->get_id());
            $portfolio->create();

            $this->publication = new Publication();
            $this->publication->set_content_object_id($portfolio->get_id());
            $this->publication->set_publisher_id($this->get_user_id());
            $this->publication->set_published(time());
            $this->publication->set_modified(time());

            if (! $this->publication->create())
            {
                throw new NotAllowedException();
            }
        }
        elseif (! $this->publication instanceof Publication)
        {
            throw new NotAllowedException();
        }

        $context = Portfolio :: package() . '\Display';
        $factory = new ApplicationFactory(
            $context,
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        return $factory->run();
    }

    /**
     *
     * @return \application\portfolio\Publication
     */
    function get_publication()
    {
        return $this->publication;
    }

    /*
     * (non-PHPdoc) @see \repository\DisplaySupport::get_root_content_object()
     */
    public function get_root_content_object()
    {
        return $this->get_publication()->get_content_object();
    }

    /**
     *
     * @see \repository\content_object\portfolio\display\PortfolioDisplaySupport::retrieve_portfolio_feedbacks()
     */
    public function retrieve_portfolio_feedbacks(ComplexContentObjectPathNode $node)
    {
        $parameters = new DataClassRetrievesParameters(
            $this->get_feedback_conditions($node),
            null,
            null,
            array(
                new OrderBy(
                    new PropertyConditionVariable(Feedback :: class_name(), Feedback :: PROPERTY_MODIFICATION_DATE),
                    SORT_DESC)));

        return DataManager :: retrieves(Feedback :: class_name(), $parameters);
    }

    /**
     *
     * @see \repository\content_object\portfolio\display\PortfolioDisplaySupport::count_portfolio_feedbacks()
     */
    public function count_portfolio_feedbacks(ComplexContentObjectPathNode $node)
    {
        $parameters = new DataClassCountParameters($this->get_feedback_conditions($node));
        return DataManager :: count(Feedback :: class_name(), $parameters);
    }

    /**
     *
     * @see \repository\content_object\portfolio\display\PortfolioDisplaySupport::retrieve_portfolio_feedback()
     */
    public function retrieve_portfolio_feedback($feedback_id)
    {
        return DataManager :: retrieve_by_id(Feedback :: class_name(), $feedback_id);
    }

    /**
     *
     * @param \repository\ComplexContentObjectPathNode $node
     * @return \libraries\storage\AndCondition
     */
    private function get_feedback_conditions($node)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Feedback :: class_name(), Feedback :: PROPERTY_COMPLEX_CONTENT_OBJECT_ID),
            $node->get_complex_content_object_item() ? new StaticConditionVariable(
                $node->get_complex_content_object_item()->get_id()) : null);
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Feedback :: class_name(), Feedback :: PROPERTY_PUBLICATION_ID),
            new StaticConditionVariable($this->get_publication()->get_id()));

        if (! $this->is_allowed_to_view_feedback($node))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Feedback :: class_name(), Feedback :: PROPERTY_USER_ID),
                new StaticConditionVariable($this->get_rights_user_id()));
        }

        return new AndCondition($conditions);
    }

    /**
     *
     * @see \repository\content_object\portfolio\display\PortfolioDisplaySupport::get_portfolio_feedback()
     */
    public function get_portfolio_feedback()
    {
        $feedback = new Feedback();
        $feedback->set_publication_id($this->get_publication()->get_id());
        return $feedback;
    }

    /**
     *
     * @see \repository\content_object\portfolio\display\PortfolioDisplaySupport::get_portfolio_tree_menu_url()
     */
    public function get_portfolio_tree_menu_url()
    {
        return Path :: getInstance()->getBasePath(true) . 'index.php?' . Application :: PARAM_CONTEXT . '=' .
             Manager :: context() . '&' . Application :: PARAM_ACTION . '=' . Manager :: ACTION_HOME . '&' .
             Manager :: PARAM_USER_ID . '=' . $this->get_current_user_id() . '&' .
             \Chamilo\Core\Repository\ContentObject\Portfolio\Display\Manager :: PARAM_STEP . '=%s';
    }

    /**
     *
     * @see \repository\content_object\portfolio\display\PortfolioDisplaySupport::is_allowed_to_update_feedback()
     */
    public function is_allowed_to_update_feedback($feedback)
    {
        return $feedback->get_user_id() == $this->get_user_id();
    }

    /**
     *
     * @see \repository\content_object\portfolio\display\PortfolioDisplaySupport::is_allowed_to_delete_feedback()
     */
    public function is_allowed_to_delete_feedback($feedback)
    {
        return $feedback->get_user_id() == $this->get_user_id();
    }

    /**
     *
     * @see \repository\content_object\portfolio\display\PortfolioDisplaySupport::is_allowed_to_create_feedback()
     */
    public function is_allowed_to_create_feedback(ComplexContentObjectPathNode $node = null)
    {
        $is_publisher = $this->get_rights_user_id() == $this->get_publication()->get_publisher_id();
        $has_right = Rights :: get_instance()->is_allowed(
            Rights :: GIVE_FEEDBACK_RIGHT,
            $this->get_location($node),
            $this->get_rights_user_id());

        return $is_publisher || $has_right;
    }

    /**
     *
     * @see \repository\content_object\portfolio\display\PortfolioDisplaySupport::is_allowed_to_view_feedback()
     */
    public function is_allowed_to_view_feedback(ComplexContentObjectPathNode $node = null)
    {
        $is_publisher = $this->get_rights_user_id() == $this->get_publication()->get_publisher_id();
        $has_right = Rights :: get_instance()->is_allowed(
            Rights :: VIEW_FEEDBACK_RIGHT,
            $this->get_location($node),
            $this->get_rights_user_id());

        return $is_publisher || $has_right;
    }

    /**
     *
     * @see \repository\content_object\portfolio\display\PortfolioDisplaySupport::is_allowed_to_edit_content_object()
     */
    public function is_allowed_to_edit_content_object(ComplexContentObjectPathNode $node = null)
    {
        $isPublisher = $this->get_publication()->get_publisher_id() == $this->get_rights_user_id();

        $contextEditRight = Rights :: get_instance()->is_allowed(
            Rights :: EDIT_RIGHT,
            $this->get_location($node),
            $this->get_rights_user_id());

        $contentObjectEditRight = RightsService :: getInstance()->canEditContentObject(
            $this->get_user(),
            $node->get_content_object());

        return $isPublisher || $contextEditRight || $contentObjectEditRight;
    }

    /**
     *
     * @see \repository\content_object\portfolio\display\PortfolioDisplaySupport::is_allowed_to_view_content_object()
     */
    public function is_allowed_to_view_content_object(ComplexContentObjectPathNode $node = null)
    {
        $is_publisher = $this->get_rights_user_id() == $this->get_publication()->get_publisher_id();
        $has_right = Rights :: get_instance()->is_allowed(
            Rights :: VIEW_RIGHT,
            $this->get_location($node),
            $this->get_rights_user_id());

        return $is_publisher || $has_right;
    }

    /**
     *
     * @see \repository\content_object\portfolio\display\PortfolioBookmarkSupport::get_portfolio_bookmark()
     */
    public function get_portfolio_bookmark($current_step)
    {
        $content_object = new Bookmark();
        $content_object->set_title(
            Translation :: get('BookmarkTitle', array('NAME' => $this->get_user()->get_fullname())));
        $content_object->set_description(
            Translation :: get('BookmarkDescription', array('NAME' => $this->get_user()->get_fullname())));
        $content_object->set_application(__NAMESPACE__);
        $content_object->set_url(
            $this->get_url(
                array(
                    \Chamilo\Core\Repository\ContentObject\Portfolio\Display\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\Portfolio\Display\Manager :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT,
                    \Chamilo\Core\Repository\ContentObject\Portfolio\Display\Manager :: PARAM_STEP => $current_step)));
        $content_object->set_owner_id($this->get_user_id());

        return $content_object;
    }

    /*
     * (non-PHPdoc) @see
     * \repository\content_object\portfolio\display\PortfolioDisplaySupport::get_portfolio_additional_tabs()
     */
    public function get_portfolio_additional_tabs()
    {
        return array(
            new DynamicVisualTab(
                self :: ACTION_BROWSE,
                Translation :: get('BrowserComponent'),
                Theme :: getInstance()->getImagePath('Chamilo\Application\Portfolio', 'Tab/' . self :: ACTION_BROWSE),
                $this->get_url(
                    array(self :: PARAM_ACTION => self :: ACTION_BROWSE),
                    array(
                        self :: PARAM_USER_ID,
                        \Chamilo\Core\Repository\ContentObject\Portfolio\Display\Manager :: PARAM_ACTION,
                        \Chamilo\Core\Repository\ContentObject\Portfolio\Display\Manager :: PARAM_STEP)),
                false,
                false,
                DynamicVisualTab :: POSITION_LEFT,
                DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
    }

    /**
     *
     * @see \repository\content_object\portfolio\display\PortfolioComplexRights::get_locations()
     */
    public function get_locations($nodes)
    {
        $locations = array();

        foreach ($nodes as $node)
        {
            $locations[] = $this->get_location($node);
        }

        return $locations;
    }

    /**
     *
     * @param ComplexContentObjectPathNode $node
     * @return \application\portfolio\RightsLocation
     */
    public function get_location(ComplexContentObjectPathNode $node = null)
    {
        return Rights :: get_instance()->get_location($node, $this->get_publication()->get_id());
    }

    /**
     *
     * @see \repository\content_object\portfolio\display\PortfolioComplexRights::get_available_rights()
     */
    public function get_available_rights()
    {
        return Rights :: get_available_rights();
    }

    /**
     *
     * @see \repository\content_object\portfolio\display\PortfolioComplexRights::get_entities()
     */
    public function get_entities()
    {
        $entities = array();
        $entities[UserEntity :: ENTITY_TYPE] = new UserEntity();
        $entities[PlatformGroupEntity :: ENTITY_TYPE] = new PlatformGroupEntity();
        return $entities;
    }

    /**
     *
     * @see \repository\content_object\portfolio\display\PortfolioComplexRights::get_selected_entities()
     */
    public function get_selected_entities(ComplexContentObjectPathNode $node)
    {
        $location = $this->get_location($node);

        return DataManager :: retrieve_rights_location_rights_for_location(
            $location->get_publication_id(),
            $location->get_node_id(),
            $this->get_available_rights());
    }

    /**
     *
     * @see \repository\content_object\portfolio\display\PortfolioComplexRights::invert_location_entity_right()
     */
    public function invert_location_entity_right($right_id, $entity_id, $entity_type, $location_id)
    {
        return Rights :: get_instance()->invert_location_entity_right(
            $right_id,
            $entity_id,
            $entity_type,
            $location_id,
            $this->get_publication()->get_id());
    }

    /**
     *
     * @see \repository\content_object\portfolio\display\PortfolioComplexRights::is_allowed_to_set_content_object_rights()
     */
    public function is_allowed_to_set_content_object_rights()
    {
        return $this->get_user()->is_platform_admin() ||
             ($this->get_user_id() == $this->get_publication()->get_publisher_id());
    }

    /**
     *
     * @see \repository\content_object\portfolio\display\PortfolioComplexRights::retrieve_portfolio_possible_view_users()
     */
    public function retrieve_portfolio_possible_view_users($condition, $count, $offset, $order_property)
    {
        return \Chamilo\Core\User\Storage\DataManager :: retrieves(
            \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
            new DataClassRetrievesParameters($condition, $count, $offset, $order_property));
    }

    /**
     *
     * @see \repository\content_object\portfolio\display\PortfolioComplexRights::count_portfolio_possible_view_users()
     */
    public function count_portfolio_possible_view_users($condition)
    {
        return \Chamilo\Core\User\Storage\DataManager :: count(
            \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
            new DataClassCountParameters($condition));
    }

    /**
     *
     * @see \repository\content_object\portfolio\display\PortfolioComplexRights::set_portfolio_virtual_user_id()
     */
    public function set_portfolio_virtual_user_id($virtual_user_id)
    {
        $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
            \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
            $virtual_user_id);

        if ($user instanceof \Chamilo\Core\User\Storage\DataClass\User)
        {
            $emulation = $this->get_emulation_storage();
            $emulation[\Chamilo\Core\Repository\ContentObject\Portfolio\Display\Manager :: PARAM_VIRTUAL_USER_ID] = $virtual_user_id;
            Session :: register(__NAMESPACE__, serialize($emulation));
            $this->virtual_user = $user;

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * @see \repository\content_object\portfolio\display\PortfolioComplexRights::clear_virtual_user_id()
     */
    public function clear_virtual_user_id()
    {
        $emulation = $this->get_emulation_storage();
        unset($emulation[\Chamilo\Core\Repository\ContentObject\Portfolio\Display\Manager :: PARAM_VIRTUAL_USER_ID]);
        Session :: register(__NAMESPACE__, serialize($emulation));
        unset($this->virtual_user);
        unset($this->rights_user_id);

        return true;
    }

    /**
     *
     * @see \repository\content_object\portfolio\display\PortfolioComplexRights::get_portfolio_virtual_user()
     */
    public function get_portfolio_virtual_user()
    {
        if (! isset($this->virtual_user))
        {
            $emulation = $this->get_emulation_storage();
            $this->virtual_user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                $emulation[\Chamilo\Core\Repository\ContentObject\Portfolio\Display\Manager :: PARAM_VIRTUAL_USER_ID]);
        }

        return $this->virtual_user;
    }

    /**
     *
     * @return string[]
     */
    private function get_emulation_storage()
    {
        return (array) unserialize(Session :: retrieve(__NAMESPACE__));
    }

    /**
     * Get the user_id that should be used for rights checks
     *
     * @return int
     */
    private function get_rights_user_id()
    {
        if (! isset($this->rights_user_id))
        {
            if ($this instanceof PortfolioComplexRights && $this->is_allowed_to_set_content_object_rights())
            {
                $virtual_user = $this->get_portfolio_virtual_user();

                if ($virtual_user instanceof \Chamilo\Core\User\Storage\DataClass\User)
                {
                    $this->rights_user_id = $virtual_user->get_id();
                }
                else
                {
                    $this->rights_user_id = $this->get_user_id();
                }
            }
            else
            {
                $this->rights_user_id = $this->get_user_id();
            }
        }

        return $this->rights_user_id;
    }

    /**
     *
     * @see \libraries\architecture\application\Application::get_additional_parameters()
     */
    public function get_additional_parameters()
    {
        return array(self :: PARAM_USER_ID);
    }

    /**
     *
     * @see \repository\content_object\portfolio\display\PortfolioDisplaySupport::is_own_portfolio()
     */
    public function is_own_portfolio()
    {
        return $this->get_user_id() == $this->get_publication()->get_publisher_id();
    }

    /**
     *
     * @see \core\repository\content_object\portfolio\display\PortfolioDisplaySupport::retrieve_portfolio_notification()
     */
    public function retrieve_portfolio_notification(
        \Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode $node)
    {
        $complex_content_object_id = $node->get_complex_content_object_item() ? $node->get_complex_content_object_item()->get_id() : 0;

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Notification :: class_name(), Notification :: PROPERTY_PUBLICATION_ID),
            new StaticConditionVariable($this->get_publication()->get_id()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                Notification :: class_name(),
                Notification :: PROPERTY_COMPLEX_CONTENT_OBJECT_ID),
            new StaticConditionVariable($complex_content_object_id));
        $condition = new AndCondition($conditions);

        return DataManager :: retrieve(Notification :: class_name(), new DataClassRetrieveParameters($condition));
    }

    /**
     *
     * @see \core\repository\content_object\portfolio\display\PortfolioDisplaySupport::get_portfolio_notification()
     */
    public function get_portfolio_notification()
    {
        $notification = new Notification();
        $notification->set_publication_id($this->get_publication()->get_id());
        return $notification;
    }
}