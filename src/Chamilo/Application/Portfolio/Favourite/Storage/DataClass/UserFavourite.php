<?php
namespace Chamilo\Application\Portfolio\Favourite\Storage\DataClass;

use Chamilo\Application\Portfolio\Favourite\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Describes the favourite of a user for another user
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserFavourite extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_FAVOURITE_USER_ID = 'favourite_user_id';
    public const PROPERTY_SOURCE_USER_ID = 'source_user_id';

    /**
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [self::PROPERTY_SOURCE_USER_ID, self::PROPERTY_FAVOURITE_USER_ID]
        );
    }

    /**
     * @return int
     */
    public function getFavouriteUserId()
    {
        return $this->getDefaultProperty(self::PROPERTY_FAVOURITE_USER_ID);
    }

    /**
     * @return int
     */
    public function getSourceUserId()
    {
        return $this->getDefaultProperty(self::PROPERTY_SOURCE_USER_ID);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'portfolio_user_favourite';
    }

    /**
     * @param int $favouriteUserId
     */
    public function setFavouriteUserId($favouriteUserId)
    {
        $this->setDefaultProperty(self::PROPERTY_FAVOURITE_USER_ID, $favouriteUserId);
    }

    /**
     * @param int $sourceUserId
     */
    public function setSourceUserId($sourceUserId)
    {
        $this->setDefaultProperty(self::PROPERTY_SOURCE_USER_ID, $sourceUserId);
    }
}