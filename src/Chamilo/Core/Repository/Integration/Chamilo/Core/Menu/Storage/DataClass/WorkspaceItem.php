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
class WorkspaceItem extends Item
{
    public const CONTEXT = 'Chamilo\Core\Repository\Integration\Chamilo\Core\Menu';

    public const PROPERTY_NAME = 'name';
    public const PROPERTY_WORKSPACE_ID = 'workspace_id';

    public function __construct($default_properties = [], $additionalProperties = [])
    {
        parent::__construct($default_properties, $additionalProperties);
        $this->setType(__CLASS__);
    }

    public static function getAdditionalPropertyNames(): array
    {
        return [self::PROPERTY_WORKSPACE_ID, self::PROPERTY_NAME];
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph
     */
    public function getGlyph()
    {
        return new FontAwesomeGlyph('hdd', [], null, 'fas');
    }

    public function getName()
    {
        return $this->getAdditionalProperty(self::PROPERTY_NAME);
    }

    public function getWorkspaceId()
    {
        return $this->getAdditionalProperty(self::PROPERTY_WORKSPACE_ID);
    }

    public function setName($name)
    {
        return $this->setAdditionalProperty(self::PROPERTY_NAME, $name);
    }

    public function setWorkspaceId($workspace_id)
    {
        return $this->setAdditionalProperty(self::PROPERTY_WORKSPACE_ID, $workspace_id);
    }
}
