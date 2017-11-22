<?php
namespace Chamilo\Application\Portfolio\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Portfolio\Favourite\Infrastructure\Repository\FavouriteRepository;
use Chamilo\Application\Portfolio\Favourite\Infrastructure\Service\FavouriteService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Translation\Translation;

/**
 * Renders the favourite users
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FavouriteUsers extends \Chamilo\Core\Home\Renderer\Type\Basic\BlockRenderer
{

    public function displayContent()
    {
        $html = array();
        
        $favouriteUsers = $this->getFavouriteService()->findFavouriteUsers($this->getUser());
        if ($favouriteUsers->size() > 0)
        {
            $html[] = '<ul style="list-style: none; margin: 0; padding: 0;">';
            
            while ($favouriteUser = $favouriteUsers->next_result())
            {
                $redirect = new Redirect(
                    array(
                        \Chamilo\Application\Portfolio\Manager::PARAM_CONTEXT => \Chamilo\Application\Portfolio\Manager::context(), 
                        \Chamilo\Application\Portfolio\Manager::PARAM_ACTION => \Chamilo\Application\Portfolio\Manager::ACTION_HOME, 
                        \Chamilo\Application\Portfolio\Manager::PARAM_USER_ID => $favouriteUser[FavouriteRepository::PROPERTY_USER_ID]));
                
                $portfolioURL = $redirect->getUrl();
                
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

    /**
     *
     * @return FavouriteService
     */
    public function getFavouriteService()
    {
        return new FavouriteService(new FavouriteRepository(), Translation::getInstance());
    }
}
