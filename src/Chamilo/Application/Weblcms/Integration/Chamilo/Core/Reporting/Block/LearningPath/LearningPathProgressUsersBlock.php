<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\LearningPath;

use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\ToolBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Domain\TrackingParameters;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingServiceBuilder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Format\Structure\ProgressBarRenderer;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

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
        $users = CourseDataManager::retrieve_all_course_users($course_id);

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_COURSE_ID
            ), new StaticConditionVariable($course_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_TOOL
            ), new StaticConditionVariable(
                ClassnameUtilities::getInstance()->getClassNameFromNamespace(LearningPath::class)
            )
        );
        $condition = new AndCondition($conditions);

        $order_by = array(
            new OrderBy(
                new PropertyConditionVariable(
                    ContentObjectPublication::class, ContentObjectPublication::PROPERTY_MODIFIED_DATE
                )
            )
        );

        $publication_resultset = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_content_object_publications(
            $condition, $order_by
        );

        $publications = array();
        $headings = array();
        $headings[] = Translation::get('Name');
        foreach ($publication_resultset as $publication)
        {
            $publications[] = $publication;
            $content_object = DataManager::retrieve_by_id(
                ContentObject::class, $publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]
            );

            if ($publication_resultset->count() > 5)
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
                $key, Translation::get('Name'), User::fullname(
                $user[User::PROPERTY_FIRSTNAME], $user[User::PROPERTY_LASTNAME]
            )
            );

            foreach ($publications as $publication)
            {
                /** @var LearningPath $content_object */
                $content_object = DataManager::retrieve_by_id(
                    ContentObject::class, $publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]
                );

                if ($publication_resultset->count() > 5)
                {
                    $title = substr($content_object->get_title(), 0, 14);
                }
                else
                {
                    $title = $content_object->get_title();
                }

                $trackingService = $this->createTrackingServiceForPublicationAndCourse(
                    $publication[ContentObjectPublication::PROPERTY_ID],
                    $publication[ContentObjectPublication::PROPERTY_COURSE_ID]
                );

                $userObject = new User();
                $userObject->setId($user[User::PROPERTY_ID]);

                $tree = $this->getLearningPathService()->getTree($content_object);

                $progressBarRenderer = new ProgressBarRenderer();

                $progress = $progressBarRenderer->render(
                    $trackingService->getLearningPathProgress($content_object, $userObject, $tree->getRoot())
                );

                $reporting_data->add_data_category_row($key, $title, $progress);
            }
        }
        $reporting_data->hide_categories();

        return $reporting_data;
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
     * @return \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    protected function getDataClassRepository()
    {
        $container = DependencyInjectionContainerBuilder::getInstance()->createContainer();

        return $container->get('Chamilo\Libraries\Storage\DataManager\Doctrine\DataClassRepository');
    }

    /**
     * Returns the LearningPathService service
     *
     * @return LearningPathService | object
     */
    protected function getLearningPathService()
    {
        $container = DependencyInjectionContainerBuilder::getInstance()->createContainer();

        return $container->get(LearningPathService::class);
    }

    /**
     *
     * @return TrackingServiceBuilder | object
     */
    protected function getTrackingServiceBuilder()
    {
        return new TrackingServiceBuilder($this->getDataClassRepository());
    }

    public function get_views()
    {
        return array(Html::VIEW_TABLE);
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }
}
