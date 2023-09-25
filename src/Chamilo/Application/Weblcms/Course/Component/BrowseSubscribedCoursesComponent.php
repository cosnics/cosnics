<?php
namespace Chamilo\Application\Weblcms\Course\Component;

use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Application\Weblcms\Course\Table\SubscribedCourseTableRenderer;

/**
 * This class describes a browser for the subscribed courses
 *
 * @package \application\weblcms\course
 * @author  Yannick & Tristan
 * @author  Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class BrowseSubscribedCoursesComponent extends BrowseSubscriptionCoursesComponent
{
    public function getSubscribedCourseTableRenderer(): SubscribedCourseTableRenderer
    {
        return $this->getService(SubscribedCourseTableRenderer::class);
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \TableException
     * @throws \Exception
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems = DataManager::count_user_courses($this->getUser(), $this->getCourseCondition());
        $subscribedCourseTableRenderer = $this->getSubscribedCourseTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $subscribedCourseTableRenderer->getParameterNames(),
            $subscribedCourseTableRenderer->getDefaultParameterValues(), $totalNumberOfItems
        );

        $courses = DataManager::retrieve_users_courses_with_course_type(
            $this->getUser(), $this->getCourseCondition(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $subscribedCourseTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $subscribedCourseTableRenderer->render($tableParameterValues, $courses);
    }
}
