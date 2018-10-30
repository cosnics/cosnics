<?php
namespace Chamilo\Application\Portfolio\Table\User;

use Chamilo\Application\Portfolio\Favourite\Infrastructure\Service\FavouriteService;
use Chamilo\Application\Portfolio\Favourite\Manager as FavouriteManager;
use Chamilo\Application\Portfolio\Manager;
use Chamilo\Application\Portfolio\Service\PublicationService;
use Chamilo\Application\Portfolio\Service\RightsService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package Chamilo\Application\Portfolio\Table\User
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     *
     * @var \Chamilo\Application\Portfolio\Service\RightsService
     */
    private $rightsService;

    /**
     *
     * @var \Chamilo\Application\Portfolio\Favourite\Infrastructure\Service\FavouriteService
     */
    private $favouriteService;

    /**
     *
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     *
     * @var \Chamilo\Application\Portfolio\Service\PublicationService
     */
    private $publicationService;

    /**
     *
     * @var \Chamilo\Libraries\Format\Theme
     */
    private $themeUtilities;

    /**
     *
     * @param \Chamilo\Libraries\Format\Table\Table $table
     * @param \Chamilo\Application\Portfolio\Service\RightsService $rightsService
     */
    public function __construct($table, RightsService $rightsService, FavouriteService $favouriteService,
        Translator $translator, PublicationService $publicationService, Theme $themeUtilities)
    {
        parent::__construct($table);

        $this->rightsService = $rightsService;
        $this->favouriteService = $favouriteService;
        $this->translator = $translator;
        $this->publicationService = $publicationService;
        $this->themeUtilities = $themeUtilities;
    }

    /**
     *
     * @return \Chamilo\Application\Portfolio\Service\RightsService
     */
    public function getRightsService()
    {
        return $this->rightsService;
    }

    /**
     *
     * @param \Chamilo\Application\Portfolio\Service\RightsService $rightsService
     */
    public function setRightsService(RightsService $rightsService)
    {
        $this->rightsService = $rightsService;
    }

    /**
     *
     * @return \Chamilo\Application\Portfolio\Favourite\Infrastructure\Service\FavouriteService
     */
    public function getFavouriteService()
    {
        return $this->favouriteService;
    }

    /**
     *
     * @param \Chamilo\Application\Portfolio\Favourite\Infrastructure\Service\FavouriteService $favouriteService
     */
    public function setFavouriteService(FavouriteService $favouriteService)
    {
        $this->favouriteService = $favouriteService;
    }

    /**
     *
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     *
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     *
     * @return \Chamilo\Application\Portfolio\Service\PublicationService
     */
    public function getPublicationService()
    {
        return $this->publicationService;
    }

    /**
     *
     * @param \Chamilo\Application\Portfolio\Service\PublicationService $publicationService
     */
    public function setPublicationService(PublicationService $publicationService)
    {
        $this->publicationService = $publicationService;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Theme
     */
    public function getThemeUtilities()
    {
        return $this->themeUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Theme $themeUtilities
     */
    public function setThemeUtilities(Theme $themeUtilities)
    {
        $this->themeUtilities = $themeUtilities;
    }

    /**
     * Returns the actions toolbar
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return string
     */
    public function get_actions($user)
    {
        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);

        if ($this->canViewUserPortfolio($user))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $this->getTranslator()->trans('ShowPortfolio', array('USER' => $user->get_fullname())),
                    $this->getThemeUtilities()->getCommonImagePath('Action/Browser'),
                    $this->get_component()->get_url(
                        array(Manager::PARAM_ACTION => Manager::ACTION_HOME, Manager::PARAM_USER_ID => $user->getId())),
                    ToolbarItem::DISPLAY_ICON));

            $favouriteService = $this->getFavouriteService();
            $favouriteContext = FavouriteManager::context();

            $possibleFavouriteUser = new User();
            $possibleFavouriteUser->setId($user->getId());

            if (! $favouriteService->isUserFavourite($this->get_component()->getUser(), $possibleFavouriteUser))
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        $this->getTranslator()->trans('CreateFavourite', [], $favouriteContext),
                        $this->getThemeUtilities()->getImagePath($favouriteContext, 'CreateFavourite'),
                        $this->get_component()->get_url(
                            array(
                                Manager::PARAM_ACTION => Manager::ACTION_BROWSE_FAVOURITES,
                                FavouriteManager::PARAM_ACTION => FavouriteManager::ACTION_CREATE,
                                FavouriteManager::PARAM_FAVOURITE_USER_ID => $user->getId())),
                        ToolbarItem::DISPLAY_ICON));
            }
            else
            {
                $favouriteUser = $favouriteService->findUserFavouriteBySourceAndFavouriteUser(
                    $this->get_component()->getUser(),
                    $possibleFavouriteUser);

                $toolbar->add_item(
                    new ToolbarItem(
                        $this->getTranslator()->trans('DeleteFavourite', [], $favouriteContext),
                        $this->getThemeUtilities()->getImagePath($favouriteContext, 'DeleteFavourite'),
                        $this->get_component()->get_url(
                            array(
                                Manager::PARAM_ACTION => Manager::ACTION_BROWSE_FAVOURITES,
                                FavouriteManager::PARAM_ACTION => FavouriteManager::ACTION_DELETE,
                                FavouriteManager::PARAM_FAVOURITE_ID => $favouriteUser->getId(),
                                FavouriteManager::PARAM_SOURCE => FavouriteManager::SOURCE_USER_BROWSER)),
                        ToolbarItem::DISPLAY_ICON,
                        true));
            }
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $this->getTranslator()->trans('ShowPortfolioNotAllowed', array('USER' => $user->get_fullname())),
                    $this->getThemeUtilities()->getCommonImagePath('Action/BrowserNa'),
                    null,
                    ToolbarItem::DISPLAY_ICON));
        }

        return $toolbar->render();
    }

    /**
     * Determine whether or not the currently logged-in user can view the user's portfolio
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return boolean
     */
    public function canViewUserPortfolio(User $user)
    {
        $userPublication = $this->getPublicationService()->getPublicationForUserIdentifier($user->getId());

        if (! $userPublication)
        {
            return true;
        }

        return $this->getRightsService()->isAllowedToViewUserPublication(
            $userPublication,
            $this->get_table()->get_component()->getUser());
    }
}