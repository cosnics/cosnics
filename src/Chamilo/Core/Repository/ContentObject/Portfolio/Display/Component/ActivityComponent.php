<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Component;

use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Service\ActivityService;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Table\ActivityTableRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;

/**
 * Component to list activity on a portfolio item
 *
 * @package repository\content_object\portfolio\display
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActivityComponent extends ItemComponent
{
    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function build()
    {
        $trail = $this->getBreadcrumbTrail();
        $trail->add(
            new Breadcrumb(
                $this->get_url([self::PARAM_STEP => $this->get_current_step()]),
                $this->getTranslator()->trans('ActivityComponent', [], self::CONTEXT)
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
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems =
            $this->getActivityService()->countActivitiesForContentObject($this->get_current_content_object());
        $activityTableRenderer = $this->getActivityTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $activityTableRenderer->getParameterNames(), $activityTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $activities = $this->getActivityService()->retrieveActivitiesForContentObject(
            $this->get_current_content_object(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $activityTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $activityTableRenderer->render($tableParameterValues, $activities);
    }
}
