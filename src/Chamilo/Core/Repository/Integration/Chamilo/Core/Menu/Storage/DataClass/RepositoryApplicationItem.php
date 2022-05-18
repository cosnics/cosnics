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
        return new FontAwesomeGlyph('hdd', [], null, 'fas');
    }

    public static function getTableName(): string
    {
        return ApplicationItem::getTableName();
    }

    public static function parentClassName(): string
    {
        return get_parent_class(ApplicationItem::class);
    }
}
