<?php
namespace Chamilo\Core\Repository\Storage\DataClass;

use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package repository.lib
 */
abstract class RepositoryDataClass extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_CREATED = 'created';
    public const PROPERTY_MODIFIED = 'modified';

    /**
     * **********************************************************************
     */
    public function __construct($defaultProperties = [])
    {
        parent::__construct($defaultProperties);
    }

    /**
     * @param $name          The name of the object property to get
     * @param $default_value A default value is the $name property is not set
     *
     * @see chamilo/common/DataClass#getDefaultProperty($name)
     */
    public function getDefaultProperty($name, $default_value = null)
    {
        $value = parent::getDefaultProperty($name);

        if (!isset($value) && isset($default_value))
        {
            $value = $default_value;
        }

        return $value;
    }

    /**
     * **********************************************************************
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_CREATED;
        $extendedPropertyNames[] = self::PROPERTY_MODIFIED;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    public function get_creation_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_CREATED);
    }

    public function get_modification_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_MODIFIED);
    }

    /**
     * **********************************************************************
     */
    public function set_creation_date($created)
    {
        if (isset($created))
        {
            $this->setDefaultProperty(self::PROPERTY_CREATED, $created);
        }
    }

    /**
     * **********************************************************************
     */
    public function set_modification_date($modified)
    {
        if (isset($modified))
        {
            $this->setDefaultProperty(self::PROPERTY_MODIFIED, $modified);
        }
    }

    /**
     * **********************************************************************
     */
}
