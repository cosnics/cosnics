<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\Overview\GroupUser;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Libraries\Format\Table\Extension\RecordListTableRenderer;

class CourseGroupUserTable extends RecordListTableRenderer
{
    const TABLE_IDENTIFIER = Manager::PARAM_OBJECT_ID;
}
