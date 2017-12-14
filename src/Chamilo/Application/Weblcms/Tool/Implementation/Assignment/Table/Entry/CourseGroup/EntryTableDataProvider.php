<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entry\CourseGroup;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\Assignment\Entry;

/**
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entry\CourseGroup
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntryTableDataProvider extends \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entry\Group\EntryTableDataProvider
{
    /**
     * @return int
     */
    function getEntityType()
    {
        return Entry::ENTITY_TYPE_COURSE_GROUP;
    }
}