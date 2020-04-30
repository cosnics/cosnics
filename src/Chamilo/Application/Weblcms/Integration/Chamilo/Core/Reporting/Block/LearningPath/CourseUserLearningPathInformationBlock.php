<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\LearningPath;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\ToolBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Domain\TrackingParameters;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingServiceBuilder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block with an overiew of the learning paths the user has
 *          attempted
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class CourseUserLearningPathInformationBlock extends ToolBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data->set_rows(
            array(Translation::get('Title'), Translation::get('Progress'), Translation::get('LearningPathDetails'))
        );
        $course_id = $this->get_parent()->get_parent()->get_parent()->get_parameter(
            \Chamilo\Application\Weblcms\Manager::PARAM_COURSE
        );
        $user_id = $this->get_user_id();

        $toolName = ClassnameUtilities::getInstance()->getClassNameFromNamespace(LearningPath::class);

        $params = array();
        $params[Application::PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE;
        $params[Application::PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager::context();
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE] = $course_id;
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL] = $toolName;
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION] =
            \Chamilo\Application\Weblcms\Tool\Manager::ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT;

        $glyph = new FontAwesomeGlyph('chart-pie');

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_TOOL
            ), new StaticConditionVariable($toolName)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_COURSE_ID
            ), new StaticConditionVariable($course_id)
        );
        $condition = new AndCondition($conditions);
        $publications_resultset =
            \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_content_object_publications(
                $condition
            );
        /*
         * $publications_arr = $publications_resultset->as_array();
         */
        $key = 0;
        while ($publication = $publications_resultset->next_result())
        {
            $progress = $url = $link = null;
            if (!\Chamilo\Application\Weblcms\Storage\DataManager::is_publication_target_user(
                $user_id, $publication[ContentObjectPublication::PROPERTY_ID]
            ))
            {
                continue;
            }
            ++ $key;

            $trackingService = $this->createTrackingServiceForPublicationAndCourse(
                $publication[ContentObjectPublication::PROPERTY_ID],
                $publication[ContentObjectPublication::PROPERTY_COURSE_ID]
            );

            /** @var LearningPath $learning_path */
            $learning_path = DataManager::retrieve_by_id(
                ContentObject::class, $publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]
            );

            if (!$learning_path instanceof LearningPath)
            {
                continue;
            }

            $params[\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION] =
                $publication[ContentObjectPublication::PROPERTY_ID];

            $params_detail = $params;
            $params_detail[Manager::PARAM_ACTION] =
                Manager::ACTION_REPORTING;
            $params_detail[Manager::PARAM_REPORTING_USER_ID] =
                $this->get_user_id();

            $link =
                '<a href="' . $this->get_parent()->get_url($params_detail) . '" target="_blank">' . $glyph->render() .
                '</a>';

            $user = new User();
            $user->setId($this->get_user_id());

            $tree = $this->getLearningPathService()->getTree($learning_path);

            $progress = $this->get_progress_bar(
                $trackingService->getLearningPathProgress($learning_path, $user, $tree->getRoot())
            );

            $redirect = new Redirect($params);
            $url = '<a href="' . $redirect->getUrl() . '" target="_blank">' . $learning_path->get_title() . '</a>';

            $reporting_data->add_category($key);
            $reporting_data->add_data_category_row($key, Translation::get('Title'), $url);
            $reporting_data->add_data_category_row($key, Translation::get('Progress'), $progress);
            $reporting_data->add_data_category_row($key, Translation::get('LearningPathDetails'), $link);
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
