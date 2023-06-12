<?php
namespace Chamilo\Application\Portfolio\Service\Home;

use Chamilo\Application\Portfolio\Favourite\Service\FavouriteService;
use Chamilo\Application\Portfolio\Favourite\Storage\Repository\FavouriteRepository;
use Chamilo\Application\Portfolio\Manager;
use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Home\Renderer\BlockRenderer;
use Chamilo\Core\Home\Rights\Service\ElementRightsService;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Portfolio\Service\Home
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FavouriteUsersBlockRenderer extends BlockRenderer
{
    public const CONTEXT = Manager::CONTEXT;

    protected FavouriteService $favouriteService;

    public function __construct(
        HomeService $homeService, UrlGenerator $urlGenerator, Translator $translator,
        ConfigurationConsulter $configurationConsulter, FavouriteService $favouriteService,
        ElementRightsService $elementRightsService
    )
    {
        parent::__construct($homeService, $urlGenerator, $translator, $configurationConsulter, $elementRightsService);

        $this->favouriteService = $favouriteService;
    }

    public function displayContent(Element $block, ?User $user = null): string
    {
        $html = [];

        $favouriteUsers = $this->getFavouriteService()->findFavouriteUsers($user);

        if ($favouriteUsers->count() > 0)
        {
            $html[] = '<ul style="list-style: none; margin: 0; padding: 0;">';

            foreach ($favouriteUsers as $favouriteUser)
            {
                $portfolioURL = $this->getUrlGenerator()->fromParameters(
                    [
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Application::PARAM_ACTION => Manager::ACTION_HOME,
                        Manager::PARAM_USER_ID => $favouriteUser[FavouriteRepository::PROPERTY_USER_ID]
                    ]
                );

                $html[] = '<li style="padding: 3px;">';
                $html[] = '<a href="' . $portfolioURL . '">';
                $html[] = $favouriteUser[User::PROPERTY_FIRSTNAME] . ' ' . $favouriteUser[User::PROPERTY_LASTNAME];
                $html[] = '</a>';
                $html[] = '</li>';
            }

            $html[] = '</ul>';
        }

        return implode(PHP_EOL, $html);
    }

    public function getFavouriteService(): FavouriteService
    {
        return $this->favouriteService;
    }
}
