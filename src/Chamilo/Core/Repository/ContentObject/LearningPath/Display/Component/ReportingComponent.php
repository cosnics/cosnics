<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\TreeNodeAttemptTableRenderer;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\TreeNodeProgressTableRenderer;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AutomaticNumberingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\PanelRenderer;
use Chamilo\Libraries\Format\Structure\ProgressBarRenderer;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Shows the progress of a user in the learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ReportingComponent extends BaseReportingComponent
{

    protected function addBreadcrumbs()
    {
        $translator = $this->getTranslator();

        $trail = $this->getBreadcrumbTrail();
        $trail->add(
            new Breadcrumb(
                $this->get_url(
                    [self::PARAM_ACTION => $this->get_action()], [self::PARAM_REPORTING_USER_ID]
                ), $translator->trans('UserProgressComponent', [], Manager::CONTEXT)
            )
        );
        $trail->add(
            new Breadcrumb(
                $this->get_url(), $translator->trans(
                'ReportingComponent', ['USER' => $this->getReportingUser()->get_fullname()], Manager::CONTEXT
            )
            )
        );
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\LearningPath\Exception\TreeNodeNotFoundException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    public function build()
    {
        $trackingService = $this->getTrackingService();
        $currentTreeNode = $this->getCurrentTreeNode();
        $automaticNumberingService = $this->getAutomaticNumberingService();
        $panelRenderer = new PanelRenderer();
        $translator = $this->getTranslator();

        $this->addBreadcrumbs();

        $html = [parent::render_header()];
        $html[] = $this->renderCommonFunctionality();

        $html[] = '<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js">';
        $html[] = '</script>';

        $html[] = '<div class="row">';

        $class = $currentTreeNode->hasChildNodes() ? 'col-lg-8' : 'col-lg-12';

        $html[] = '<div class="' . $class . ' col-md-12">';

        $html[] = $this->renderInformationPanel(
            $currentTreeNode, $automaticNumberingService, $trackingService, $panelRenderer
        );

        $html[] = '</div>';

        if ($currentTreeNode->hasChildNodes())
        {
            $html[] = '<div class="col-lg-4 col-md-12">';
            $html[] = $this->renderProgress($trackingService, $currentTreeNode, $panelRenderer);
            $html[] = '</div>';
        }

        $html[] = '</div>';

        if ($this->getCurrentTreeNode()->hasChildNodes())
        {
            $panelHtml = [];

            $panelHtml[] = $this->getTreeNodeProgressButtonToolbar()->render();
            $panelHtml[] = $this->renderTreeNodeProgressTable();

            $html[] = $panelRenderer->render(
                $translator->trans('Children', [], Manager::CONTEXT), implode(PHP_EOL, $panelHtml)
            );
        }

        $panelHtml = [];

        $panelHtml[] = $this->getTreeNodeAttemptsButtonToolbar()->render();
        $panelHtml[] = $this->renderTreeNodeAttemptTable();

        $html[] =
            $panelRenderer->render($translator->trans('Attempts', [], Manager::CONTEXT), implode(PHP_EOL, $panelHtml));

        if ($currentTreeNode->supportsScore())
        {
            $html[] = $this->renderScoreChart(
                $trackingService, $panelRenderer, $this->learningPath, $this->getReportingUser(), $currentTreeNode
            );
        }

        $html[] = ResourceManager::getInstance()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath(Manager::CONTEXT) . 'KeyboardNavigation.js'
        );

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    protected function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->getService(DatetimeUtilities::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    public function getTreeNodeAttemptTableRenderer(): TreeNodeAttemptTableRenderer
    {
        return $this->getService(TreeNodeProgressTableRenderer::class);
    }

    protected function getTreeNodeAttemptsButtonToolbar(): ButtonToolBarRenderer
    {
        $buttonToolbar = new ButtonToolBar();

        $buttonToolbar->addItem(
            new Button(
                $this->getTranslator()->trans('Export', [], StringUtilities::LIBRARIES),
                new FontAwesomeGlyph('download'), $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_EXPORT_REPORTING,
                    ReportingExporterComponent::PARAM_EXPORT => ReportingExporterComponent::EXPORT_TREE_NODE_ATTEMPTS
                ]
            )
            )
        );

        return new ButtonToolBarRenderer($buttonToolbar);
    }

    protected function getTreeNodeProgressButtonToolbar(): ButtonToolBarRenderer
    {
        $buttonToolbar = new ButtonToolBar();

        $buttonToolbar->addItem(
            new Button(
                $this->getTranslator()->trans('Export', [], StringUtilities::LIBRARIES),
                new FontAwesomeGlyph('download'), $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_EXPORT_REPORTING,
                    ReportingExporterComponent::PARAM_EXPORT => ReportingExporterComponent::EXPORT_TREE_NODE_CHILDREN_PROGRESS
                ]
            )
            )
        );

        return new ButtonToolBarRenderer($buttonToolbar);
    }

    public function getTreeNodeProgressTableRenderer(): TreeNodeProgressTableRenderer
    {
        return $this->getService(TreeNodeProgressTableRenderer::class);
    }

    // /**
    // * Builds and returns the button toolbar for this component
    // *
    // * @param Translation $translator
    // *
    // * @return \Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar
    // */
    // public function getButtonToolbar()
    // {
    // $toolbar = parent::getButtonToolbar();
    //
    // if ($this->canEditCurrentTreeNode())
    // {
    // $toolbar->prependItem(
    // new Button(
    // $translator->getTranslation('ReturnToUserList'),
    // new FontAwesomeGlyph('chart-bar'),
    // $this->get_url(
    // array(self::PARAM_ACTION => self::ACTION_VIEW_USER_PROGRESS),
    // array(self::PARAM_REPORTING_USER_ID)
    // )
    // )
    // );
    // }
    //
    // return $toolbar;
    // }

    protected function renderInformationPanel(
        TreeNode $currentTreeNode, AutomaticNumberingService $automaticNumberingService,
        TrackingService $trackingService, PanelRenderer $panelRenderer
    ): string
    {
        $translator = $this->getTranslator();

        $parentTitles = [];
        foreach ($currentTreeNode->getParentNodes() as $parentNode)
        {
            $url = $this->get_url([self::PARAM_CHILD_ID => $parentNode->getId()]);
            $title = $automaticNumberingService->getAutomaticNumberedTitleForTreeNode($parentNode);
            $parentTitles[] = '<a href="' . $url . '">' . $title . '</a>';
        }

        $informationValues = [];

        $informationValues[$translator->trans('Title', [], Manager::CONTEXT)] =
            $automaticNumberingService->getAutomaticNumberedTitleForTreeNode(
                $currentTreeNode
            );

        if (!$currentTreeNode->isRootNode())
        {
            $informationValues[$translator->trans('Parents', [], Manager::CONTEXT)] = implode(' >> ', $parentTitles);
        }

        $informationValues[$translator->trans('User', [], Manager::CONTEXT)] =
            $this->getReportingUser()->get_fullname();

        $informationValues[$translator->trans('TotalTime', [], Manager::CONTEXT)] =
            $this->getDatetimeUtilities()->formatSecondsToHours(
                $trackingService->getTotalTimeSpentInTreeNode(
                    $this->learningPath, $this->getReportingUser(), $currentTreeNode
                )
            );

        if ($currentTreeNode->supportsScore())
        {
            $progressBarRenderer = new ProgressBarRenderer();

            $informationValues[$translator->trans('AverageScore', [], Manager::CONTEXT)] = $progressBarRenderer->render(
                (int) $trackingService->getAverageScoreInTreeNode(
                    $this->learningPath, $this->getReportingUser(), $currentTreeNode
                )
            );

            $informationValues[$translator->trans('MaximumScore', [], Manager::CONTEXT)] = $progressBarRenderer->render(
                $trackingService->getMaximumScoreInTreeNode(
                    $this->learningPath, $this->getReportingUser(), $currentTreeNode
                )
            );

            $informationValues[$translator->trans('MinimumScore', [], Manager::CONTEXT)] = $progressBarRenderer->render(
                $trackingService->getMinimumScoreInTreeNode(
                    $this->learningPath, $this->getReportingUser(), $currentTreeNode
                )
            );

            $informationValues[$translator->trans('LastScore', [], Manager::CONTEXT)] = $progressBarRenderer->render(
                (int) $trackingService->getLastAttemptScoreForTreeNode(
                    $this->learningPath, $this->getReportingUser(), $currentTreeNode
                )
            );
        }

        return $panelRenderer->renderTablePanel(
            $informationValues, $translator->trans('Information', [], Manager::CONTEXT)
        );
    }

    protected function renderProgress(
        TrackingService $trackingService, TreeNode $currentTreeNode, PanelRenderer $panelRenderer
    ): string
    {
        $translator = $this->getTranslator();

        $completedLabel = $translator->trans('Completed', [], Manager::CONTEXT);
        $notCompletedLabel = $translator->trans('NotCompleted', [], Manager::CONTEXT);

        $progress = $trackingService->getLearningPathProgress(
            $this->learningPath, $this->getReportingUser(), $currentTreeNode
        );

        $notCompleted = 100 - $progress;

        $panelHtml = [];
        $panelHtml[] = '<canvas id="myChart" width="270" height="135" style="margin: auto;"></canvas>';
        $panelHtml[] = '<script>';
        $panelHtml[] = 'var ctx = document.getElementById("myChart");';
        $panelHtml[] = 'var myChart = new Chart(ctx, {';
        $panelHtml[] = '    type: "doughnut",';
        $panelHtml[] = '    data: {';
        $panelHtml[] = '        labels: ["' . $completedLabel . '", "' . $notCompletedLabel . '"],';
        $panelHtml[] = '        datasets: [{';
        $panelHtml[] = '            data: [' . $progress . ',' . $notCompleted . '],';
        $panelHtml[] = '            backgroundColor: [';
        $panelHtml[] = '                    "#36A2EB",';
        $panelHtml[] = '                    "#FF6384",';
        $panelHtml[] = '                ],';
        $panelHtml[] = '            hoverBackgroundColor: [';
        $panelHtml[] = '                    "#36A2EB",';
        $panelHtml[] = '                    "#FF6384",';
        $panelHtml[] = '                ],';
        $panelHtml[] = '            borderWidth: 1';
        $panelHtml[] = '        }]';
        $panelHtml[] = '    },';
        $panelHtml[] = '    options: {';
        $panelHtml[] = '         legend: {';
        $panelHtml[] = '             onClick: null';
        $panelHtml[] = '         },';
        $panelHtml[] = '         responsive: true,';
        $panelHtml[] = '         animation: { animateScale: true },';
        $panelHtml[] = '         legend: { position: "right" },';
        $panelHtml[] = '         tooltips: {';
        $panelHtml[] = '             callbacks: {';
        $panelHtml[] = '                 label: function(tooltipItem, data) {';
        $panelHtml[] = '                     var value = data.datasets[0].data[tooltipItem.index];';
        $panelHtml[] = '                     var label = data.labels[tooltipItem.index];';
        $panelHtml[] = '                     return " " + label + ": " + value + "%"';
        $panelHtml[] = '                 }';
        $panelHtml[] = '             }';
        $panelHtml[] = '         }';
        $panelHtml[] = '    }';
        $panelHtml[] = '});';
        $panelHtml[] = '</script>';

        return $panelRenderer->render(
            $translator->trans('Progress', [], Manager::CONTEXT), implode(PHP_EOL, $panelHtml)
        );
    }

    /**
     * Renders the scores for every attempt in a chart
     *
     * @param TrackingService $trackingService
     * @param PanelRenderer $panelRenderer
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     *
     * @return string
     */
    protected function renderScoreChart(
        TrackingService $trackingService, PanelRenderer $panelRenderer, LearningPath $learningPath, User $user,
        TreeNode $treeNode
    ): string
    {
        $translator = $this->getTranslator();

        $labels = $scores = [];

        $treeNodeAttempts = $trackingService->getTreeNodeAttempts($learningPath, $user, $treeNode);

        foreach ($treeNodeAttempts as $treeNodeAttempt)
        {
            if (!$treeNodeAttempt->isCompleted())
            {
                continue;
            }

            $labels[] = $this->getDatetimeUtilities()->formatLocaleDate(null, $treeNodeAttempt->get_start_time());
            $scores[] = (int) $treeNodeAttempt->get_score();
        }

        $html = [];

        $html[] = '<canvas id="scoreChart" style="margin: auto;"></canvas>';
        $html[] = '<script>';
        $html[] = 'var ctx = document.getElementById("scoreChart");';
        $html[] = 'var myChart = new Chart(ctx, {';
        $html[] = '    type: "line",';
        $html[] = '    data: {';
        $html[] = '        labels: ["' . implode('" , "', $labels) . '"],';
        $html[] = '        datasets: [{';
        $html[] = '            data: [' . implode(', ', $scores) . '],';
        $html[] = '            backgroundColor: "rgba(75,192,192,0.4)",';
        $html[] = '            borderColor: "rgba(75,192,192,1)",';
        $html[] = '            label: "' . $translator->trans('Scores', [], Manager::CONTEXT) . '",';
        $html[] = '            pointRadius: 5,';
        $html[] = '            fill: false';
        $html[] = '        }]';
        $html[] = '    },';
        $html[] = '    options: {';
        $html[] = '         legend: {';
        $html[] = '             onClick: null';
        $html[] = '         },';
        $html[] = '         tooltips: {';
        $html[] = '             callbacks: {';
        $html[] = '                 label: function(tooltipItem, data) {';
        $html[] = '                     var value = data.datasets[0].data[tooltipItem.index];';
        $html[] = '                     var label = data.labels[tooltipItem.index];';
        $html[] = '                     return " " + label + ": " + value + "%"';
        $html[] = '                 }';
        $html[] = '             }';
        $html[] = '         }';
        $html[] = '    }';
        $html[] = '});';
        $html[] = '</script>';

        return $panelRenderer->render($translator->trans('Scores', [], Manager::CONTEXT), implode(PHP_EOL, $html));
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\LearningPath\Exception\TreeNodeNotFoundException
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    protected function renderTreeNodeAttemptTable(): string
    {
        $treeNodeAttempts = $this->getTrackingService()->getTreeNodeAttempts(
            $this->get_root_content_object(), $this->getReportingUser(), $this->getCurrentTreeNode()
        );

        $totalNumberOfItems = count($treeNodeAttempts);

        $treeNodeAttemptTableRenderer = $this->getTreeNodeAttemptTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $treeNodeAttemptTableRenderer->getParameterNames(),
            $treeNodeAttemptTableRenderer->getDefaultParameterValues(), $totalNumberOfItems
        );

        $treeNodeAttempts = new ArrayCollection(
            array_slice(
                $treeNodeAttempts, $tableParameterValues->getOffset(), $tableParameterValues->getNumberOfItemsPerPage()
            )
        );

        return $treeNodeAttemptTableRenderer->legacyRender($this, $tableParameterValues, $treeNodeAttempts);
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\LearningPath\Exception\TreeNodeNotFoundException
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    protected function renderTreeNodeProgressTable(): string
    {
        $totalNumberOfItems = count($this->getCurrentTreeNode()->getChildNodes());

        $treeNodeProgressTableRenderer = $this->getTreeNodeProgressTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $treeNodeProgressTableRenderer->getParameterNames(),
            $treeNodeProgressTableRenderer->getDefaultParameterValues(), $totalNumberOfItems
        );

        $treeNodes = new ArrayCollection(
            array_slice(
                $this->getCurrentTreeNode()->getChildNodes(), $tableParameterValues->getOffset(),
                $tableParameterValues->getNumberOfItemsPerPage()
            )
        );

        return $treeNodeProgressTableRenderer->legacyRender($this, $tableParameterValues, $treeNodes);
    }
}
