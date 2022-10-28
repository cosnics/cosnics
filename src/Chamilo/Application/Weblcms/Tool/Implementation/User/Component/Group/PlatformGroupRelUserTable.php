<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\Group;

use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;

/**
 * * ***************************************************************************
 * Table to display the users inside a subscribed platform group.
 * 
 * @author Stijn Van Hoecke
 *         ****************************************************************************
 */
class PlatformGroupRelUserTable extends DataClassListTableRenderer
{
    const TABLE_IDENTIFIER = Manager::PARAM_OBJECT_ID;
}
