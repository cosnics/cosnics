<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entry\PlatformGroup;

use Chamilo\Core\Group\Storage\DataClass\Group;

/**
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entry\PlatformGroup
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntryTable extends \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entry\Group\EntryTable
{
    const TABLE_IDENTIFIER = Group::PROPERTY_ID;
}