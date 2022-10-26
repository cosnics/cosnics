<?php
namespace Chamilo\Application\Portfolio\Table\User;

use Chamilo\Application\Portfolio\Favourite\Manager;
use Chamilo\Application\Portfolio\Favourite\Service\FavouriteService;
use Chamilo\Application\Portfolio\Service\PublicationService;
use Chamilo\Application\Portfolio\Service\RightsService;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Theme\ThemePathBuilder;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package Chamilo\Application\Portfolio\Table\User
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserTable extends DataClassTable implements TableActionsSupport
{
    const TABLE_IDENTIFIER = \Chamilo\Application\Portfolio\Manager::PARAM_USER_ID;

    /**
     *
     * @var \Chamilo\Core\User\Service\UserService
     */
    private $userService;

    /**
     *
     * @var \Chamilo\Application\Portfolio\Service\RightsService
     */
    private $rightsService;

    /**
     *
     * @var \Chamilo\Application\Portfolio\Service\PublicationService
     */
    private $publicationService;

    /**
     *
     * @var \Chamilo\Application\Portfolio\Favourite\Service\FavouriteService
     */
    private $favouriteService;

    /**
     *
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     *
     * @var \Chamilo\Libraries\Format\Theme\ThemePathBuilder
     */
    private $themePathBuilder;

    /**
     * Constructor
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $component
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Chamilo\Application\Portfolio\Service\RightsService $rightsService
     * @param \Chamilo\Application\Portfolio\Service\PublicationService $publicationService
     * @param \Chamilo\Application\Portfolio\Favourite\Service\FavouriteService $favouriteService
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Libraries\Format\Theme\ThemePathBuilder $themePathBuilder
     *
     * @throws \Exception
     */
    public function __construct(
        $component, UserService $userService, RightsService $rightsService, PublicationService $publicationService,
        FavouriteService $favouriteService, Translator $translator, ThemePathBuilder $themePathBuilder
    )
    {
        $this->userService = $userService;
        $this->rightsService = $rightsService;
        $this->publicationService = $publicationService;
        $this->favouriteService = $favouriteService;
        $this->translator = $translator;
        $this->themePathBuilder = $themePathBuilder;

        parent::__construct($component);
    }

    /**
     *
     * @return \Chamilo\Application\Portfolio\Favourite\Service\FavouriteService
     */
    public function getFavouriteService()
    {
        return $this->favouriteService;
    }

    /**
     *
     * @param \Chamilo\Application\Portfolio\Favourite\Service\FavouriteService $favouriteService
     */
    public function setFavouriteService(FavouriteService $favouriteService)
    {
        $this->favouriteService = $favouriteService;
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
     * @return \Chamilo\Libraries\Format\Theme\ThemePathBuilder
     */
    public function getThemePathBuilder()
    {
        return $this->themePathBuilder;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Theme\ThemePathBuilder $themePathBuilder
     */
    public function setThemePathBuilder(ThemePathBuilder $themePathBuilder)
    {
        $this->themePathBuilder = $themePathBuilder;
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
     * @return \Chamilo\Core\User\Service\UserService
     */
    public function getUserService()
    {
        return $this->userService;
    }

    /**
     *
     * @param \Chamilo\Core\User\Service\UserService $userService
     */
    public function setUserService(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Gets the table's cell renderer or builds one if it is not set
     *
     * @return \Chamilo\Libraries\Format\Table\TableCellRenderer
     */
    public function getTableCellRenderer(): UserTableCellRenderer
    {
        if (!isset($this->cellRenderer))
        {
            $this->cellRenderer = new UserTableCellRenderer(
                $this, $this->getRightsService(), $this->getFavouriteService(), $this->getTranslator(),
                $this->getPublicationService(), $this->getThemePathBuilder()
            );
        }

        return $this->cellRenderer;
    }

    /**
     * @see \Chamilo\Libraries\Format\Table\Table::getTableDataProvider()
     */
    public function getTableDataProvider(): UserTableDataProvider
    {
        if (!isset($this->dataProvider))
        {
            $this->dataProvider = new UserTableDataProvider($this, $this->getUserService());
        }

        return $this->dataProvider;
    }

    /**
     * Returns the implemented form actions
     *
     * @return TableActions
     */
    public function getTableActions(): TableActions
    {
        $actions = new TableActions(Manager::context(), Manager::PARAM_FAVOURITE_USER_ID);

        $actions->addAction(
            new TableAction(
                $this->get_component()->get_url(
                    array(
                        \Chamilo\Application\Portfolio\Manager::PARAM_ACTION => \Chamilo\Application\Portfolio\Manager::ACTION_BROWSE_FAVOURITES,
                        Manager::PARAM_ACTION => Manager::ACTION_CREATE
                    )
                ), $this->getTranslator()->trans('CreateFavourites', [], Manager::context()), false
            )
        );

        return $actions;
    }
}