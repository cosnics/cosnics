<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\LearningPath;

use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\ToolBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Domain\TrackingParameters;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingServiceBuilder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Format\Structure\ProgressBarRenderer;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block with an overview of progress of each learning path
 *          per user
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class LearningPathProgressUsersBlock extends ToolBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $course_id = $this->getCourseId();
        $users = CourseDataManager::retrieve_all_course_users($course_id)->as_array();

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(),
                ContentObjectPublication::PROPERTY_COURSE_ID),
            new StaticConditionVariable($course_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(),
                ContentObjectPublication::PROPERTY_TOOL),
            new StaticConditionVariable(
                ClassnameUtilities::getInstance()->getClassNameFromNamespace(LearningPath::class_name())));
        $condition = new AndCondition($conditions);

        $order_by = new OrderBy(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(),
                ContentObjectPublication::PROPERTY_MODIFIED_DATE));

        $publication_resultset = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_content_object_publications(
            $condition,
            $order_by);

        $publications = array();
        $headings = array();
        $headings[] = Translation::get('Name');
        while ($publication = $publication_resultset->next_result())
        {
            $publications[] = $publication;
            $content_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(),
                $publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]);

            if ($publication_resultset->size() > 5)
            {
                $headings[] = substr($content_object->get_title(), 0, 14);
            }
            else
            {
                $headings[] = $content_object->get_title();
            }
        }

        $reporting_data->set_rows($headings);

        foreach ($users as $key => $user)
        {
            $reporting_data->add_category($key);
            $reporting_data->add_data_category_row(
                $key,
                Translation::get('Name'),
                \Chamilo\Core\User\Storage\DataClass\User::fullname(
                    $user[\Chamilo\Core\User\Storage\DataClass\User::PROPERTY_FIRSTNAME],
                    $user[\Chamilo\Core\User\Storage\DataClass\User::PROPERTY_LASTNAME]));

            foreach ($publications as $publication)
            {
                /** @var LearningPath $content_object */
                $content_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                    ContentObject::class_name(),
                    $publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]);

                if ($publication_resultset->size() > 5)
                {
                    $title = substr($content_object->get_title(), 0, 14);
                }
                else
                {
                    $title = $content_object->get_title();
                }

                $trackingService = $this->createTrackingServiceForPublicationAndCourse(
                    $publication[ContentObjectPublication::PROPERTY_ID],
                    $publication[ContentObjectPublication::PROPERTY_COURSE_ID]);

                $userObject = new User();
                $userObject->setId($user[User::PROPERTY_ID]);

                $tree = $this->getLearningPathService()->getTree($content_object);

                $progressBarRenderer = new ProgressBarRenderer();

                $progress = $progressBarRenderer->render(
                    $trackingService->getLearningPathProgress($content_object, $userObject, $tree->getRoot()));

                $reporting_data->add_data_category_row($key, $title, $progress);
            }
        }
        $reporting_data->hide_categories();

        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(\Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_TABLE);
    }

    /**
     * Creates the TrackingService for a given Publication and Course
     *
     * @param int $publicationId
     * @param int $courseId
     *
     * @return TrackingService
     */
    public function createTrackingServiceForPublicationAndCourse($publicationId, $courseId)
    {
        $trackingServiceBuilder = $this->getTrackingServiceBuilder();

        return $trackingServiceBuilder->buildTrackingService(new TrackingParameters((int) $publicationId));
    }

    /**
     *
     * @return TrackingServiceBuilder | object
     */
    protected function getTrackingServiceBuilder()
    {
        return new TrackingServiceBuilder($this->getDataClassRepository());
    }

    /**
     * Returns the LearningPathService service
     *
     * @return LearningPathService | object
     */
    protected function getLearningPathService()
    {
        $container = DependencyInjectionContainerBuilder::getInstance()->createContainer();

        return $container->get('chamilo.core.repository.content_object.learning_path.service.learning_path_service');
    }

    /**
     *
     * @return object | DataClassRepository
     */
    protected function getDataClassRepository()
    {
        $container = DependencyInjectionContainerBuilder::getInstance()->createContainer();

        return $container->get('chamilo.libraries.storage.data_manager.doctrine.data_class_repository');
    }
}
