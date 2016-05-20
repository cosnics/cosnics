<?php
namespace Chamilo\Application\Portfolio\Favourite;

use Chamilo\Application\Portfolio\Favourite\Infrastructure\Repository\FavouriteRepository;
use Chamilo\Application\Portfolio\Favourite\Infrastructure\Service\FavouriteService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Platform\Translation;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends Application
{
    // Parameters
    const PARAM_FAVOURITE_ID = 'favourite_id';
    const PARAM_FAVOURITE_USER_ID = 'favourite_user_id';
    const PARAM_ACTION = 'favourite_action';

    // Actions
    const ACTION_BROWSE = 'Browser';
    const ACTION_DELETE = 'Deleter';
    const ACTION_CREATE = 'Creator';

    // Default action
    const DEFAULT_ACTION = self :: ACTION_BROWSE;

    /**
     * The favourite service
     *
     * @var FavouriteService
     */
    protected $favouriteService;

    /**
     * Returns the favourite service
     * 
     * @return FavouriteService
     */
    public function getFavouriteService()
    {
        if(!isset($this->favouriteService))
        {
            $this->favouriteService = new FavouriteService(new FavouriteRepository(), Translation::getInstance());
        }

        return $this->favouriteService;
    }
}
