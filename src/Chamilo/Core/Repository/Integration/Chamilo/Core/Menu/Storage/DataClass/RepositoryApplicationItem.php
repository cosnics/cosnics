<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass;

use Chamilo\Core\Menu\Storage\DataClass\ApplicationItem;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;

/**
 *
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RepositoryApplicationItem extends ApplicationItem
{

    /**
     * @return \Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph
     */
    public function getGlyph()
    {
        return new FontAwesomeGlyph('hdd', array(), null, 'fas');
    }

    public static function get_table_name()
    {
        return ApplicationItem::get_table_name();
    }

    /**
     * @inheritdoc
     * Override needed because the database table parent is Item, not ApplicationItem.
     * See Chamilo\Libraries\Storage\DataManager\Doctrine\Database Create function
     *
     * @return string
     */
    public static function parent_class_name()
    {
        return get_parent_class(ApplicationItem::class_name());
    }
}
