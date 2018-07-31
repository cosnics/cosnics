<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;

/**
 *
 * @package application.weblcms.tool.assignment.php.component Component for the submission feedback form for assignment
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Bert De Clercq (Hogeschool Gent)
 * @author Anthony Hurst (Hogeschool Gent)
 */
class SubmissionSubmitComponent extends Manager
{

    /** Leave this for old redirects */
    public function run()
    {
        $this->redirect(null, false, [self::PARAM_ACTION => self::ACTION_DISPLAY]);
    }
}
