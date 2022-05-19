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
class WorkspaceCategoryItem extends Item
{

    private $children;

    public function __construct($default_properties = [], $additionalProperties = [])
    {
        parent::__construct($default_properties, $additionalProperties);
        $this->setType(__CLASS__);
    }

    public function add_child($child)
    {
        $this->children[] = $child;
    }

    public function delete(): bool
    {
        foreach ($this->get_children() as $child)
        {
            if (!$child->delete())
            {
                return false;
            }
        }

        return parent::delete();
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph
     */
    public function getGlyph()
    {
        return new FontAwesomeGlyph('server', [], null, 'fas');
    }

    public function get_children()
    {
        if (!isset($this->children))
        {
            $this->children = [];

            $configurationItem = new WorkspaceConfigureItem();
            $configurationItem->set_parent($this->get_id());
            $configurationItem->set_display($this->get_display());

            $this->children[] = $configurationItem;
        }

        return $this->children;
    }

    public function set_children($children)
    {
        $this->children = $children;
    }

    public static function getTypeName(): string
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class);
    }

    public function has_children()
    {
        return count($this->get_children()) > 0;
    }
}
