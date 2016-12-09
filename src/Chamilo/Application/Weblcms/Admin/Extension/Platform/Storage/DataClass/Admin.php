<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package Chamilo\Application\Weblcms\Admin\Extension\Platform\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Admin extends DataClass
{
    
    // Properties
    const PROPERTY_ORIGIN = 'origin';
    const PROPERTY_ENTITY_TYPE = 'entity_type';
    const PROPERTY_ENTITY_ID = 'entity_id';
    const PROPERTY_TARGET_TYPE = 'target_type';
    const PROPERTY_TARGET_ID = 'target_id';
    const PROPERTY_CREATED = 'created';
    const PROPERTY_MODIFIED = 'modified';
    
    // Origins
    const ORIGIN_EXTERNAL = 1;
    const ORIGIN_INTERNAL = 2;

    /**
     * Get the default property names
     * 
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_ORIGIN, 
                self::PROPERTY_ENTITY_TYPE, 
                self::PROPERTY_ENTITY_ID, 
                self::PROPERTY_TARGET_TYPE, 
                self::PROPERTY_TARGET_ID, 
                self::PROPERTY_CREATED, 
                self::PROPERTY_MODIFIED));
    }

    public function get_origin()
    {
        return $this->get_default_property(self::PROPERTY_ORIGIN);
    }

    public function set_origin($origin)
    {
        $this->set_default_property(self::PROPERTY_ORIGIN, $origin);
    }

    public function get_entity_type()
    {
        return $this->get_default_property(self::PROPERTY_ENTITY_TYPE);
    }

    public function set_entity_type($entity_type)
    {
        $this->set_default_property(self::PROPERTY_ENTITY_TYPE, $entity_type);
    }

    public function get_entity_id()
    {
        return $this->get_default_property(self::PROPERTY_ENTITY_ID);
    }

    public function set_entity_id($entity_id)
    {
        $this->set_default_property(self::PROPERTY_ENTITY_ID, $entity_id);
    }

    public function get_target_type()
    {
        return $this->get_default_property(self::PROPERTY_TARGET_TYPE);
    }

    public function set_target_type($target_type)
    {
        $this->set_default_property(self::PROPERTY_TARGET_TYPE, $target_type);
    }

    public function get_target_id()
    {
        return $this->get_default_property(self::PROPERTY_TARGET_ID);
    }

    public function set_target_id($target_id)
    {
        $this->set_default_property(self::PROPERTY_TARGET_ID, $target_id);
    }

    public function get_created()
    {
        return $this->get_default_property(self::PROPERTY_CREATED);
    }

    public function set_created($created)
    {
        $this->set_default_property(self::PROPERTY_CREATED, $created);
    }

    public function get_modified()
    {
        return $this->get_default_property(self::PROPERTY_MODIFIED);
    }

    public function set_modified($modified)
    {
        $this->set_default_property(self::PROPERTY_MODIFIED, $modified);
    }

    public function create($create_in_batch = false)
    {
        $now = time();
        $this->set_created($now);
        $this->set_modified($now);
        
        return parent::create();
    }

    public function update()
    {
        $this->set_modified(time());
        
        return parent::update();
    }
}
