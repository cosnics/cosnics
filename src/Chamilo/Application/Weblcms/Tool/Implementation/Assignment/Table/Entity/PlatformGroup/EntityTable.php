<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entity\PlatformGroup;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entity\PlatformGroup
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityTable extends \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entity\Group\EntityTable
{
    const TABLE_IDENTIFIER = Group::PROPERTY_ID;
}