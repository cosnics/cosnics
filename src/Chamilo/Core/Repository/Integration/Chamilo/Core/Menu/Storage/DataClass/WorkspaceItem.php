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
class WorkspaceItem extends Item
{
    const PROPERTY_WORKSPACE_ID = 'workspace_id';
    const PROPERTY_NAME = 'name';

    public function __construct($default_properties = [], $additionalProperties = [])
    {
        parent::__construct($default_properties, $additionalProperties);
        $this->set_type(__CLASS__);
    }

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class);
    }

    public function getWorkspaceId()
    {
        return $this->getAdditionalProperty(self::PROPERTY_WORKSPACE_ID);
    }

    public function setWorkspaceId($workspace_id)
    {
        return $this->setAdditionalProperty(self::PROPERTY_WORKSPACE_ID, $workspace_id);
    }

    public function getName()
    {
        return $this->getAdditionalProperty(self::PROPERTY_NAME);
    }

    public function setName($name)
    {
        return $this->setAdditionalProperty(self::PROPERTY_NAME, $name);
    }

    public static function getAdditionalPropertyNames(): array
    {
        return array(self::PROPERTY_WORKSPACE_ID, self::PROPERTY_NAME);
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph
     */
    public function getGlyph()
    {
        return new FontAwesomeGlyph('hdd', [], null, 'fas');
    }
}
