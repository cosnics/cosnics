<?php
namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Core\Admin\Service\BreadcrumbGenerator;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbGeneratorInterface;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbLessComponentInterface;

/**
 * This class represents a component that runs the course type submanager
 *
 * @package \application\weblcms\course_type
 * @author  Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class CourseTypeManagerComponent extends Manager implements BreadcrumbLessComponentInterface
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageCourses');

        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Application\Weblcms\CourseType\Manager::CONTEXT,
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
        )->run();
    }

    public function getBreadcrumbGenerator(): BreadcrumbGeneratorInterface
    {
        return $this->getService(BreadcrumbGenerator::class);
    }
}
