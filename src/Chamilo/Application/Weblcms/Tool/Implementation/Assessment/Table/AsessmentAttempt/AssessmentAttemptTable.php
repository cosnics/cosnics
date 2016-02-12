<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Table\AsessmentAttempt;

use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Manager;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTable;

/**
 * This class represents a table for the attempts of an assessment
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssessmentAttemptTable extends RecordTable
{
    const TABLE_IDENTIFIER = Manager :: PARAM_USER_ASSESSMENT;
}
