<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 *
 * @package application.weblcms.tool.assignment.php.component
 *          This class can be used to toggle the visibility of assignments
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class ToggleVisibilityComponent extends Manager
{
    public function run()
    {
        throw new NotAllowedException();
    }
}
