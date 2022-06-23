<?php
namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
        $component =
            $this->isAuthorized(Manager::context(), 'ViewPersonalCourses') ? 'CourseList' : 'OpenCoursesBrowser';

        return new RedirectResponse(
            $this->getUrlGenerator()->fromParameters(
                [self::PARAM_CONTEXT => Manager::context(), self::PARAM_ACTION => $component]
            )
        );
    }
}
