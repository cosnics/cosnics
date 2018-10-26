<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;

/**
 * Displays the students' version of the assignments browser.
 *
 * @author Anthony Hurst (Hogeschool Gent)
 * @author Bert De Clercq (Hogeschool Gent)
 */
class StudentSubmissionsBrowserComponent extends Manager
{
    /** Leave this for old redirects */
    public function run()
    {
        $this->redirect(null, false, [self::PARAM_ACTION => self::ACTION_DISPLAY]);
    }

}
