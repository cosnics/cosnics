<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform\Storage\DataClass;

use Chamilo\Application\Weblcms\Admin\Extension\Platform\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Application\Weblcms\Admin\Extension\Platform\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Admin extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const ORIGIN_EXTERNAL = 1;
    public const ORIGIN_INTERNAL = 2;

    public const PROPERTY_CREATED = 'created';
    public const PROPERTY_ENTITY_ID = 'entity_id';
    public const PROPERTY_ENTITY_TYPE = 'entity_type';
    public const PROPERTY_MODIFIED = 'modified';
    public const PROPERTY_ORIGIN = 'origin';
    public const PROPERTY_TARGET_ID = 'target_id';
    public const PROPERTY_TARGET_TYPE = 'target_type';

    public function create(): bool
    {
        $now = time();
        $this->set_created($now);
        $this->set_modified($now);

        return parent::create();
    }

    /**
     * Get the default property names
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_ORIGIN,
                self::PROPERTY_ENTITY_TYPE,
                self::PROPERTY_ENTITY_ID,
                self::PROPERTY_TARGET_TYPE,
                self::PROPERTY_TARGET_ID,
                self::PROPERTY_CREATED,
                self::PROPERTY_MODIFIED
            ]
        );
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'weblcms_admin';
    }

    public function get_created()
    {
        return $this->getDefaultProperty(self::PROPERTY_CREATED);
    }

    public function get_entity_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_ENTITY_ID);
    }

    public function get_entity_type()
    {
        return $this->getDefaultProperty(self::PROPERTY_ENTITY_TYPE);
    }

    public function get_modified()
    {
        return $this->getDefaultProperty(self::PROPERTY_MODIFIED);
    }

    public function get_origin()
    {
        return $this->getDefaultProperty(self::PROPERTY_ORIGIN);
    }

    public function get_target_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_TARGET_ID);
    }

    public function get_target_type()
    {
        return $this->getDefaultProperty(self::PROPERTY_TARGET_TYPE);
    }

    public function set_created($created)
    {
        $this->setDefaultProperty(self::PROPERTY_CREATED, $created);
    }

    public function set_entity_id($entity_id)
    {
        $this->setDefaultProperty(self::PROPERTY_ENTITY_ID, $entity_id);
    }

    public function set_entity_type($entity_type)
    {
        $this->setDefaultProperty(self::PROPERTY_ENTITY_TYPE, $entity_type);
    }

    public function set_modified($modified)
    {
        $this->setDefaultProperty(self::PROPERTY_MODIFIED, $modified);
    }

    public function set_origin($origin)
    {
        $this->setDefaultProperty(self::PROPERTY_ORIGIN, $origin);
    }

    public function set_target_id($target_id)
    {
        $this->setDefaultProperty(self::PROPERTY_TARGET_ID, $target_id);
    }

    public function set_target_type($target_type)
    {
        $this->setDefaultProperty(self::PROPERTY_TARGET_TYPE, $target_type);
    }

    public function update(): bool
    {
        $this->set_modified(time());

        return parent::update();
    }
}
