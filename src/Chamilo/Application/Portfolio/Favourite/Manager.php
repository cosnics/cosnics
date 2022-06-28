<?php
namespace Chamilo\Application\Portfolio\Favourite;

use Chamilo\Application\Portfolio\Favourite\Service\FavouriteService;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends Application
{
    public const ACTION_BROWSE = 'Browser';
    public const ACTION_CREATE = 'Creator';
    public const ACTION_DELETE = 'Deleter';

    public const DEFAULT_ACTION = self::ACTION_BROWSE;

    public const PARAM_ACTION = 'favourite_action';
    public const PARAM_FAVOURITE_ID = 'favourite_id';
    public const PARAM_FAVOURITE_USER_ID = 'favourite_user_id';
    public const PARAM_SOURCE = 'source';

    public const SOURCE_FAVOURITES_BROWSER = 'favourites_browser';
    public const SOURCE_USER_BROWSER = 'user_browser';

    /**
     *
     * @return \Chamilo\Application\Portfolio\Favourite\Service\FavouriteService
     */
    public function getFavouriteService()
    {
        return $this->getService(FavouriteService::class);
    }
}
