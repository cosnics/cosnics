<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;

/**
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class RepositoryImplementationItem extends Item
{
    public const CONTEXT = 'Chamilo\Core\Repository\Integration\Chamilo\Core\Menu';

    public const PROPERTY_IMPLEMENTATION = 'implementation';
    public const PROPERTY_INSTANCE_ID = 'instance_id';
    public const PROPERTY_NAME = 'name';

    public function __construct($default_properties = [], $additionalProperties = [])
    {
        parent::__construct($default_properties, $additionalProperties);
        $this->setType(__CLASS__);
    }

    public static function getAdditionalPropertyNames(): array
    {
        return [self::PROPERTY_IMPLEMENTATION, self::PROPERTY_INSTANCE_ID, self::PROPERTY_NAME];
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph
     */
    public function getGlyph()
    {
        return new FontAwesomeGlyph('globe', [], null, 'fas');
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
