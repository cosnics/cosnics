<?php
namespace Chamilo\Application\Portfolio\Table;

use Chamilo\Application\Portfolio\Favourite\Manager;
use Chamilo\Application\Portfolio\Favourite\Service\FavouriteService;
use Chamilo\Application\Portfolio\Service\PublicationService;
use Chamilo\Application\Portfolio\Service\RightsService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Portfolio\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const TABLE_IDENTIFIER = \Chamilo\Application\Portfolio\Manager::PARAM_USER_ID;

    private FavouriteService $favouriteService;

    private PublicationService $publicationService;

    private RightsService $rightsService;

    private User $user;

    public function __construct(
        RightsService $rightsService, PublicationService $publicationService, FavouriteService $favouriteService,
        Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        User $user
    )
    {
        $this->rightsService = $rightsService;
        $this->publicationService = $publicationService;
        $this->favouriteService = $favouriteService;
        $this->user = $user;

        parent::__construct($translator, $urlGenerator, $htmlTableRenderer, $pager);
    }

    public function canViewUserPortfolio(User $user): bool
    {
        $userPublication = $this->getPublicationService()->findPublicationForUserIdentifier($user->getId());

        if (!$userPublication)
        {
            return true;
        }

        return $this->getRightsService()->isAllowedToViewUserPublication(
            $userPublication, $this->getUser()
        );
    }

    public function getFavouriteService(): FavouriteService
    {
        return $this->favouriteService;
    }

    public function getPublicationService(): PublicationService
    {
        return $this->publicationService;
    }

    public function getRightsService(): RightsService
    {
        return $this->rightsService;
    }

    public function getTableActions(): TableActions
    {
        $urlGenerator = $this->getUrlGenerator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $createUrl = $urlGenerator->fromParameters([
            Application::PARAM_CONTEXT => \Chamilo\Application\Portfolio\Manager::CONTEXT,
            Application::PARAM_ACTION => \Chamilo\Application\Portfolio\Manager::ACTION_BROWSE_FAVOURITES,
            Manager::PARAM_ACTION => Manager::ACTION_CREATE
        ]);

        $actions->addAction(
            new TableAction(
                $createUrl, $this->getTranslator()->trans('CreateFavourites', [], Manager::CONTEXT), false
            )
        );

        return $actions;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    protected function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_FIRSTNAME));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_LASTNAME));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_OFFICIAL_CODE));
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $user): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        if ($this->canViewUserPortfolio($user))
        {
            $homeUrl = $urlGenerator->fromParameters([
                Application::PARAM_CONTEXT => \Chamilo\Application\Portfolio\Manager::CONTEXT,
                Application::PARAM_ACTION => \Chamilo\Application\Portfolio\Manager::ACTION_HOME,
                \Chamilo\Application\Portfolio\Manager::PARAM_USER_ID => $user->getId()
            ]);

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('ShowPortfolio', ['USER' => $user->get_fullname()]),
                    new FontAwesomeGlyph('desktop'), $homeUrl, ToolbarItem::DISPLAY_ICON
                )
            );

            $favouriteService = $this->getFavouriteService();
            $favouriteContext = Manager::CONTEXT;

            $possibleFavouriteUser = new User();
            $possibleFavouriteUser->setId($user->getId());

            if (!$favouriteService->isUserFavourite($this->getUser(), $possibleFavouriteUser))
            {
                $createUrl = $urlGenerator->fromParameters([
                    Application::PARAM_CONTEXT => \Chamilo\Application\Portfolio\Manager::CONTEXT,
                    Application::PARAM_ACTION => \Chamilo\Application\Portfolio\Manager::ACTION_BROWSE_FAVOURITES,
                    Manager::PARAM_ACTION => Manager::ACTION_CREATE,
                    Manager::PARAM_FAVOURITE_USER_ID => $user->getId()
                ]);

                $toolbar->add_item(
                    new ToolbarItem(
                        $translator->trans('CreateFavourite', [], $favouriteContext),
                        new FontAwesomeGlyph('star', [], null, 'fas'), $createUrl, ToolbarItem::DISPLAY_ICON
                    )
                );
            }
            else
            {
                $favouriteUser = $favouriteService->findUserFavouriteBySourceAndFavouriteUser(
                    $this->getUser(), $possibleFavouriteUser
                );

                $deleteUrl = $urlGenerator->fromParameters([
                    Application::PARAM_CONTEXT => \Chamilo\Application\Portfolio\Manager::CONTEXT,
                    Application::PARAM_ACTION => \Chamilo\Application\Portfolio\Manager::ACTION_BROWSE_FAVOURITES,
                    Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                    Manager::PARAM_FAVOURITE_ID => $favouriteUser->getId(),
                    Manager::PARAM_SOURCE => Manager::SOURCE_USER_BROWSER
                ]);

                $toolbar->add_item(
                    new ToolbarItem(
                        $translator->trans('DeleteFavourite', [], $favouriteContext),
                        new FontAwesomeGlyph('star', [], null, 'far'), $deleteUrl, ToolbarItem::DISPLAY_ICON, true
                    )
                );
            }
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('ShowPortfolioNotAllowed', ['USER' => $user->get_fullname()]),
                    new FontAwesomeGlyph('desktop', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->render();
    }
}
