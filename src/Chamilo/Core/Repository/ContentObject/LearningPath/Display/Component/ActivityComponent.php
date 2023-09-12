<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Service\ActivityService;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Table\ActivityTableRenderer;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Translation\Translation;

/**
 * Component to list activity on a portfolio item
 *
 * @package repository\content_object\portfolio\display
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActivityComponent extends BaseHtmlTreeComponent implements DelegateComponent
{

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\LearningPath\Exception\TreeNodeNotFoundException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    public function build()
    {
        $this->validateSelectedTreeNodeData();

        $trail = $this->getBreadcrumbTrail();
        $trail->add(
            new Breadcrumb(
                $this->get_url([self::PARAM_CHILD_ID => $this->getCurrentTreeNodeDataId()]),
                Translation::get('ActivityComponent')
            )
        );

        $html = [];

        $html[] = $this->render_header();
        $html[] = $this->renderTable();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function getActivityService(): ActivityService
    {
        return $this->getService(ActivityService::class);
    }

    public function getActivityTableRenderer(): ActivityTableRenderer
    {
        return $this->getService(ActivityTableRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\LearningPath\Exception\TreeNodeNotFoundException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems =
            $this->getActivityService()->countActivitiesForContentObject($this->getCurrentContentObject());
        $activityTableRenderer = $this->getActivityTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $activityTableRenderer->getParameterNames(), $activityTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $activities = $this->getActivityService()->retrieveActivitiesForContentObject(
            $this->getCurrentContentObject(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $activityTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $activityTableRenderer->render($tableParameterValues, $activities);
    }
}
