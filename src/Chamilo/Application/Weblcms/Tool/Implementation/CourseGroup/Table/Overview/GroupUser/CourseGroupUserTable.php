<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\Overview\GroupUser;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Libraries\Format\Table\Extension\RecordTable;

class CourseGroupUserTable extends RecordTable
{
    const TABLE_IDENTIFIER = Manager::PARAM_OBJECT_ID;
}
