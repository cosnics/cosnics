<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;

/**
 *
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RepositoryImplementationItem extends Item
{
    const PROPERTY_IMPLEMENTATION = 'implementation';
    const PROPERTY_INSTANCE_ID = 'instance_id';
    const PROPERTY_NAME = 'name';

    public function __construct($default_properties = [], $additionalProperties = [])
    {
        parent::__construct($default_properties, $additionalProperties);
        $this->setType(__CLASS__);
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph
     */
    public function getGlyph()
    {
        return new FontAwesomeGlyph('globe', [], null, 'fas');
    }

    public static function getAdditionalPropertyNames(): array
    {
        return array(self::PROPERTY_IMPLEMENTATION, self::PROPERTY_INSTANCE_ID, self::PROPERTY_NAME);
    }

    public function get_implementation()
    {
        return $this->getAdditionalProperty(self::PROPERTY_IMPLEMENTATION);
    }

    public function get_instance_id()
    {
        return $this->getAdditionalProperty(self::PROPERTY_INSTANCE_ID);
    }

    public function get_name()
    {
        return $this->getAdditionalProperty(self::PROPERTY_NAME);
    }

    public static function getTypeName(): string
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class);
    }

    public function set_implementation($implementation)
    {
        return $this->setAdditionalProperty(self::PROPERTY_IMPLEMENTATION, $implementation);
    }

    public function set_instance_id($instance_id)
    {
        return $this->setAdditionalProperty(self::PROPERTY_INSTANCE_ID, $instance_id);
    }

    public function set_name($name)
    {
        return $this->setAdditionalProperty(self::PROPERTY_NAME, $name);
    }
}
