<?php
namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
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
        $factory = new ApplicationFactory(
            \Chamilo\Application\Weblcms\Course\OpenCourse\Manager::context(), 
            new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this));
        
        return $factory->run();
    }
}