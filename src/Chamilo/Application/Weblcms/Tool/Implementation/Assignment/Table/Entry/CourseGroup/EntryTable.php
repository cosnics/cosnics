<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entry\CourseGroup;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;

/**
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entry\CourseGroup
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntryTable extends \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entry\Group\EntryTable
{
    const TABLE_IDENTIFIER = CourseGroup::PROPERTY_ID;
}