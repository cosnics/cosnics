<?php
namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

/**
 * Browser for the open courses
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class OpenCoursesBrowserComponent extends Manager implements DelegateComponent
{

    /**
     * Runs this component and returns it's output
     */
    public function run()
    {
        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Application\Weblcms\Course\OpenCourse\Manager::CONTEXT,
            new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this))->run();
    }
}