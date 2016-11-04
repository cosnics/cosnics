<?php
namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Redirect;

/**
 *
 * @package Chamilo\Application\Weblcms\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class HomeComponent extends Manager implements DelegateComponent
{

    /**
     * Runs this component and returns it's output
     */
    public function run()
    {
        $component = $this->isAuthorized(Manager::context(), 'ViewPersonalCourses') ? 'CourseList' : 'OpenCoursesBrowser';

        $redirect = new Redirect(array(self::PARAM_CONTEXT => Manager::context(), self::PARAM_ACTION => $component));

        $redirect->toUrl();
    }
}
