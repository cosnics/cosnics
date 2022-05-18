<?php
namespace Chamilo\Application\Portfolio\Storage\DataClass;

use Chamilo\Application\Portfolio\Storage\DataManager;

/**
 *
 * @package Chamilo\Application\Portfolio\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsLocationEntityRight extends \Chamilo\Core\Rights\RightsLocationEntityRight
{
    // DataClass properties
    const PROPERTY_PUBLICATION_ID = 'publication_id';

    /**
     *
     * @return DataManager
     */
    public function get_data_manager()
    {
        return DataManager::getInstance();
    }

    /**
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames($extendedPropertyNames = []): array
    {
        $default_property_names = parent::getDefaultPropertyNames();
        $default_property_names[] = self::PROPERTY_PUBLICATION_ID;

        return $default_property_names;
    }

    /**
     *
     * @return int
     */
    public function get_publication_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_PUBLICATION_ID);
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'portfolio_rights_location_entity_right';
    }

    /**
     *
     * @param int $publication_id
     */
    public function set_publication_id($publication_id)
    {
        $this->setDefaultProperty(self::PROPERTY_PUBLICATION_ID, $publication_id);
    }
}