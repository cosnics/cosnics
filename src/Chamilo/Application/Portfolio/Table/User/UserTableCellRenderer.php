<?php
namespace Chamilo\Application\Portfolio\Table\User;

use Chamilo\Application\Portfolio\Favourite\Infrastructure\Repository\FavouriteRepository;
use Chamilo\Application\Portfolio\Favourite\Infrastructure\Service\FavouriteService;
use Chamilo\Application\Portfolio\Manager;
use Chamilo\Application\Portfolio\Rights;
use Chamilo\Application\Portfolio\Storage\DataClass\Publication;
use Chamilo\Application\Portfolio\Storage\DataClass\RightsLocation;
use Chamilo\Application\Portfolio\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Table cell renderer
 * 
 * @package application\portfolio
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     * Returns the actions toolbar
     * 
     * @param \user\User $result
     *
     * @return string
     */
    public function get_actions($result)
    {
        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);
        
        if ($this->can_view_user_portfolio($result) || $result->get_id() == $this->get_component()->get_user_id())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('ShowPortfolio', array('USER' => $result->get_fullname())), 
                    Theme::getInstance()->getCommonImagePath('Action/Browser'), 
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_HOME, 
                            Manager::PARAM_USER_ID => $result->get_id())), 
                    ToolbarItem::DISPLAY_ICON));
            
            $favouriteService = new FavouriteService(new FavouriteRepository(), Translation::getInstance());
            $favouriteContext = \Chamilo\Application\Portfolio\Favourite\Manager::context();
            
            $possibleFavouriteUser = new User();
            $possibleFavouriteUser->setId($result->getId());
            
            if (! $favouriteService->isUserFavourite($this->get_component()->getUser(), $possibleFavouriteUser))
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::getInstance()->getTranslation('CreateFavourite', null, $favouriteContext), 
                        Theme::getInstance()->getImagePath($favouriteContext, 'CreateFavourite'), 
                        $this->get_component()->get_url(
                            array(
                                Manager::PARAM_ACTION => Manager::ACTION_BROWSE_FAVOURITES, 
                                \Chamilo\Application\Portfolio\Favourite\Manager::PARAM_ACTION => \Chamilo\Application\Portfolio\Favourite\Manager::ACTION_CREATE, 
                                \Chamilo\Application\Portfolio\Favourite\Manager::PARAM_FAVOURITE_USER_ID => $result->getId())), 
                        ToolbarItem::DISPLAY_ICON));
            }
            else
            {
                $favouriteUser = $favouriteService->findUserFavouriteBySourceAndFavouriteUser(
                    $this->get_component()->getUser(), 
                    $possibleFavouriteUser);
                
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::getInstance()->getTranslation('DeleteFavourite', null, $favouriteContext), 
                        Theme::getInstance()->getImagePath($favouriteContext, 'DeleteFavourite'), 
                        $this->get_component()->get_url(
                            array(
                                Manager::PARAM_ACTION => Manager::ACTION_BROWSE_FAVOURITES, 
                                \Chamilo\Application\Portfolio\Favourite\Manager::PARAM_ACTION => \Chamilo\Application\Portfolio\Favourite\Manager::ACTION_DELETE, 
                                \Chamilo\Application\Portfolio\Favourite\Manager::PARAM_FAVOURITE_ID => $favouriteUser->getId(), 
                                \Chamilo\Application\Portfolio\Favourite\Manager::PARAM_SOURCE => \Chamilo\Application\Portfolio\Favourite\Manager::SOURCE_USER_BROWSER)), 
                        ToolbarItem::DISPLAY_ICON, 
                        true));
            }
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('ShowPortfolioNotAllowed', array('USER' => $result->get_fullname())), 
                    Theme::getInstance()->getCommonImagePath('Action/BrowserNa'), 
                    null, 
                    ToolbarItem::DISPLAY_ICON));
        }
        
        return $toolbar->as_html();
    }

    /**
     * Determine whether or not the currently logged-in user can view the user's portfolio
     * 
     * @param \user\User $result
     *
     * @return boolean
     */
    public function can_view_user_portfolio($result)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_PUBLISHER_ID), 
            new StaticConditionVariable($result->get_id()));
        $user_publication = DataManager::retrieve(
            Publication::class_name(), 
            new DataClassRetrieveParameters($condition));
        
        if (! $user_publication)
        {
            return true;
        }
        
        $node_id = md5(serialize(array($user_publication->get_content_object_id())));
        
        $location = new RightsLocation();
        
        $location->set_node_id($node_id);
        $location->set_publication_id($user_publication->get_id());
        $location->set_inherit(0);
        $location->set_parent_id(null);
        
        $is_publisher = $this->get_table()->get_component()->get_user_id() == $result->get_id();
        $has_right = Rights::getInstance()->is_allowed(
            Rights::VIEW_RIGHT, 
            $location, 
            $this->get_table()->get_component()->get_user_id());
        
        return $is_publisher || $has_right;
    }
}