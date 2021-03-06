<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathTreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\AssignmentServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Domain\AssignmentConfiguration;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\TreeNodeAttempt\TreeNodeAttemptTable;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\TreeNodeProgress\TreeNodeProgressTable;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AutomaticNumberingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\PanelRenderer;
use Chamilo\Libraries\Format\Structure\ProgressBarRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Shows the progress of a user in the learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ReportingComponent extends BaseReportingComponent implements TableSupport
{

    /**
     *
     * @return string
     */
    public function build()
    {
        $translator = Translation::getInstance();
        $trackingService = $this->getTrackingService();
        $currentTreeNode = $this->getCurrentTreeNode();
        $automaticNumberingService = $this->getAutomaticNumberingService();
        $panelRenderer = new PanelRenderer();

        $this->addBreadcrumbs($translator);

        $html = [parent::render_header()];
        $html[] = $this->renderCommonFunctionality();

        $html[] = '<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js">';
        $html[] = '</script>';

        $html[] = '<div class="row">';

        $class = $currentTreeNode->hasChildNodes() ? 'col-lg-8' : 'col-lg-12';

        $html[] = '<div class="' . $class . ' col-md-12">';

        $html[] = $this->renderInformationPanel(
            $currentTreeNode,
            $automaticNumberingService,
            $translator,
            $trackingService,
            $panelRenderer
        );

        $html[] = '</div>';

        if ($currentTreeNode->hasChildNodes())
        {
            $html[] = '<div class="col-lg-4 col-md-12">';
            $html[] = $this->renderProgress($translator, $trackingService, $currentTreeNode, $panelRenderer);
            $html[] = '</div>';
        }

        $html[] = '</div>';

        if ($this->getCurrentTreeNode()->hasChildNodes())
        {
            $table = new TreeNodeProgressTable($this);
            $panelHtml = array();

            $panelHtml[] = $this->getTreeNodeProgressButtonToolbar($translator)->render();
            $panelHtml[] = $table->as_html();

            $html[] = $panelRenderer->render($translator->getTranslation('Children'), implode(PHP_EOL, $panelHtml));
        }

        $table = new TreeNodeAttemptTable($this);
        $panelHtml = array();

        $panelHtml[] = $this->getTreeNodeAttemptsButtonToolbar($translator)->render();
        $panelHtml[] = $table->as_html();

        $html[] = $panelRenderer->render($translator->getTranslation('Attempts'), implode(PHP_EOL, $panelHtml));

        if ($currentTreeNode->supportsScore())
        {
            $html[] = $this->renderScoreChart(
                $trackingService,
                $translator,
                $panelRenderer,
                $this->learningPath,
                $this->getReportingUser(),
                $currentTreeNode
            );
        }

        if ($this->getCurrentTreeNode()->isRootNode())
        {
            $panelHtml = array();
            $panelHtml[] = $this->renderScoreOverview();
            $html[] =
                $panelRenderer->render($translator->getTranslation('ScoreOverview'), implode(PHP_EOL, $panelHtml));
        }

        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath(Manager::package(), true) . 'KeyboardNavigation.js'
        );

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the button toolbar for the TreeNodeProgressTable block
     *
     * @param Translation $translator
     *
     * @return ButtonToolBarRenderer
     */
    protected function getTreeNodeProgressButtonToolbar(Translation $translator)
    {
        $buttonToolbar = new ButtonToolBar();

        $buttonToolbar->addItem(
            new Button(
                $translator->getTranslation('Export', null, Utilities::COMMON_LIBRARIES),
                new FontAwesomeGlyph('download'),
                $this->get_url(
                    [
                        self::PARAM_ACTION => self::ACTION_EXPORT_REPORTING,
                        ReportingExporterComponent::PARAM_EXPORT => ReportingExporterComponent::EXPORT_TREE_NODE_CHILDREN_PROGRESS
                    ]
                )
            )
        );

        return new ButtonToolBarRenderer($buttonToolbar);
    }

    /**
     * Returns the button toolbar for the TreeNodeProgressTable block
     *
     * @param Translation $translator
     *
     * @return ButtonToolBarRenderer
     */
    protected function getTreeNodeAttemptsButtonToolbar(Translation $translator)
    {
        $buttonToolbar = new ButtonToolBar();

        $buttonToolbar->addItem(
            new Button(
                $translator->getTranslation('Export', null, Utilities::COMMON_LIBRARIES),
                new FontAwesomeGlyph('download'),
                $this->get_url(
                    [
                        self::PARAM_ACTION => self::ACTION_EXPORT_REPORTING,
                        ReportingExporterComponent::PARAM_EXPORT => ReportingExporterComponent::EXPORT_TREE_NODE_ATTEMPTS
                    ]
                )
            )
        );

        return new ButtonToolBarRenderer($buttonToolbar);
    }

    /**
     * Adds the breadcrumbs for this component
     *
     * @param Translation $translator
     */
    protected function addBreadcrumbs(Translation $translator)
    {
        $trail = BreadcrumbTrail::getInstance();
        $trail->add(
            new Breadcrumb(
                $this->get_url(
                    array(self::PARAM_ACTION => $this->get_action()),
                    array(self::PARAM_REPORTING_USER_ID)
                ),
                $translator->getTranslation('UserProgressComponent')
            )
        );
        $trail->add(
            new Breadcrumb(
                $this->get_url(),
                $translator->getTranslation(
                    'ReportingComponent',
                    array('USER' => $this->getReportingUser()->get_fullname())
                )
            )
        );
    }

    /**
     * Renders the information panel
     *
     * @param TreeNode $currentTreeNode
     * @param AutomaticNumberingService $automaticNumberingService
     * @param Translation $translator
     * @param TrackingService $trackingService
     * @param PanelRenderer $panelRenderer
     *
     * @return string
     */
    protected function renderInformationPanel(
        TreeNode $currentTreeNode,
        AutomaticNumberingService $automaticNumberingService, Translation $translator, TrackingService $trackingService,
        PanelRenderer $panelRenderer
    )
    {
        $parentTitles = array();
        foreach ($currentTreeNode->getParentNodes() as $parentNode)
        {
            $url = $this->get_url(array(self::PARAM_CHILD_ID => $parentNode->getId()));
            $title = $automaticNumberingService->getAutomaticNumberedTitleForTreeNode($parentNode);
            $parentTitles[] = '<a href="' . $url . '">' . $title . '</a>';
        }

        $informationValues = [];

        $informationValues[$translator->getTranslation('Title')] =
            $automaticNumberingService->getAutomaticNumberedTitleForTreeNode(
                $currentTreeNode
            );

        if (!$currentTreeNode->isRootNode())
        {
            $informationValues[$translator->getTranslation('Parents')] = implode(' >> ', $parentTitles);
        }

        $informationValues[$translator->getTranslation('User')] = $this->getReportingUser()->get_fullname();

        $informationValues[$translator->getTranslation('TotalTime')] = DatetimeUtilities::format_seconds_to_hours(
            $trackingService->getTotalTimeSpentInTreeNode(
                $this->learningPath,
                $this->getReportingUser(),
                $currentTreeNode
            )
        );

        if ($currentTreeNode->supportsScore())
        {
            $progressBarRenderer = new ProgressBarRenderer();

            $informationValues[$translator->getTranslation('AverageScore')] = $progressBarRenderer->render(
                (int) $trackingService->getAverageScoreInTreeNode(
                    $this->learningPath,
                    $this->getReportingUser(),
                    $currentTreeNode
                )
            );

            $informationValues[$translator->getTranslation('MaximumScore')] = $progressBarRenderer->render(
                $trackingService->getMaximumScoreInTreeNode(
                    $this->learningPath,
                    $this->getReportingUser(),
                    $currentTreeNode
                )
            );

            $informationValues[$translator->getTranslation('MinimumScore')] = $progressBarRenderer->render(
                $trackingService->getMinimumScoreInTreeNode(
                    $this->learningPath,
                    $this->getReportingUser(),
                    $currentTreeNode
                )
            );

            $informationValues[$translator->getTranslation('LastScore')] = $progressBarRenderer->render(
                $trackingService->getLastCompletedAttemptScoreForTreeNode(
                    $this->learningPath,
                    $this->getReportingUser(),
                    $currentTreeNode
                )
            );
        }

        return $panelRenderer->renderTablePanel($translator->getTranslation('Information'), $informationValues);
    }

    /**
     * Renders the progress doughnut chart
     *
     * @param Translation $translator
     * @param TrackingService $trackingService
     * @param TreeNode $currentTreeNode
     * @param PanelRenderer $panelRenderer
     *
     * @return string
     */
    protected function renderProgress(
        Translation $translator, TrackingService $trackingService,
        TreeNode $currentTreeNode, PanelRenderer $panelRenderer
    )
    {
        $completedLabel = $translator->getTranslation('Completed');
        $notCompletedLabel = $translator->getTranslation('NotCompleted');

        $progress = $trackingService->getLearningPathProgress(
            $this->learningPath,
            $this->getReportingUser(),
            $currentTreeNode
        );

        $notCompleted = 100 - $progress;

        $panelHtml = array();
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

        return $panelRenderer->render($translator->getTranslation('Progress'), implode(PHP_EOL, $panelHtml));
    }

    /**
     * Renders the scores for every attempt in a chart
     *
     * @param TrackingService $trackingService
     * @param Translation $translator
     * @param PanelRenderer $panelRenderer
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     *
     * @return string
     */
    protected function renderScoreChart(
        TrackingService $trackingService, Translation $translator,
        PanelRenderer $panelRenderer, LearningPath $learningPath, User $user, TreeNode $treeNode
    )
    {
        $labels = $scores = [];

        $treeNodeAttempts = $trackingService->getTreeNodeAttempts($learningPath, $user, $treeNode);

        foreach ($treeNodeAttempts as $treeNodeAttempt)
        {
            if (!$treeNodeAttempt->isCompleted())
            {
                continue;
            }

            $labels[] = DatetimeUtilities::format_locale_date(null, $treeNodeAttempt->get_start_time());
            $scores[] = (int) $treeNodeAttempt->get_score();
        }

        $html = array();

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
        $html[] = '            label: "' . $translator->getTranslation('Scores') . '",';
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

        return $panelRenderer->render($translator->getTranslation('Scores'), implode(PHP_EOL, $html));
    }

    // /**
    // * Builds and returns the button toolbar for this component
    // *
    // * @param Translation $translator
    // *
    // * @return \Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar
    // */
    // public function getButtonToolbar(Translation $translator)
    // {
    // $toolbar = parent::getButtonToolbar($translator);
    //
    // if ($this->canEditCurrentTreeNode())
    // {
    // $toolbar->prependItem(
    // new Button(
    // $translator->getTranslation('ReturnToUserList'),
    // new FontAwesomeGlyph('bar-chart'),
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

    /**
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    protected function renderScoreOverview()
    {
        $scoreData = [];

        $trackingService = $this->getTrackingService();
        $tree = $this->getTree();
        $user = $this->getReportingUser();

        foreach ($tree->getTreeNodes() as $treeNode)
        {
            if ($treeNode->supportsScore())
            {
                $reportingUrl = $this->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_REPORTING,
                        Manager::PARAM_CHILD_ID => $treeNode->getId()
                    )
                );

                $lastResultUrl = $this->getLastResultUrl($treeNode);

                $scoreData[] = [
                    'type' => $treeNode->getContentObject()->get_icon_image(),
                    'title' => $this->getAutomaticNumberingService()->getAutomaticNumberedTitleForTreeNode($treeNode),
                    'reporting_url' => $reportingUrl,
                    'path' => $this->getLearningPathService()->renderPathForTreeNode($treeNode),
                    'number_of_attempts' =>
                        $trackingService->countTreeNodeAttempts($this->learningPath, $user, $treeNode),
                    'minimum_score' => $trackingService->getMinimumScoreInTreeNode(
                        $this->learningPath, $user, $treeNode
                    ),
                    'maximum_score' => $trackingService->getMaximumScoreInTreeNode(
                        $this->learningPath, $user, $treeNode
                    ),
                    'average_score' => $trackingService->getAverageScoreInTreeNode(
                        $this->learningPath, $user, $treeNode
                    ),
                    'last_score' => $trackingService->getLastCompletedAttemptScoreForTreeNode(
                        $this->learningPath, $user, $treeNode
                    ),
                    'last_result_url' => $lastResultUrl
                ];
            }
        }

        $exportUrl = $this->get_url(
            [
                self::PARAM_ACTION => self::ACTION_EXPORT_REPORTING,
                ReportingExporterComponent::PARAM_EXPORT => ReportingExporterComponent::EXPORT_SCORE_OVERVIEW
            ]
        );

        return $this->getTwig()->render(
            Manager::context() . ':ScoreOverviewReporting.html.twig',
            ['SCORE_DATA' => $scoreData, 'EXPORT_URL' => $exportUrl]
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return string|null
     */
    protected function getLastResultUrl(TreeNode $treeNode)
    {
        $user = $this->getReportingUser();

        $treeNodeAttempts = $this->trackingService->getTreeNodeAttempts($this->learningPath, $user, $treeNode);

        do
        {
            $lastAttempt = array_pop($treeNodeAttempts);
        }
        while($lastAttempt instanceof LearningPathTreeNodeAttempt && !$lastAttempt->isCompleted());

        if(empty($lastAttempt))
        {
            return null;
        }

        if($treeNode->getContentObject() instanceof Assessment)
        {
            return $this->get_url(
                array(
                    Manager::PARAM_ACTION => Manager::ACTION_VIEW_ASSESSMENT_RESULT,
                    Manager::PARAM_CHILD_ID => $treeNode->getId(),
                    Manager::PARAM_ITEM_ATTEMPT_ID => $lastAttempt->getId()
                )
            );
        }

        if($treeNode->getContentObject() instanceof Assignment)
        {
            /** @var AssignmentConfiguration $configuration */
            $configuration = $treeNode->getConfiguration(new AssignmentConfiguration());
            $entityType = $configuration->getEntityType();

            if($entityType != 0)
            {
                $entry = $this->getAssignmentServiceBridge()->findEntryForLearningPathAttempt($lastAttempt->getId());
                if(!$entry instanceof Entry)
                {
                    return null;
                }

                $entityId = $entry->getEntityId();
            }
            else
            {
                $entityId = $user->getId();
            }

            return $this->get_url(
                array(
                    Manager::PARAM_ACTION => Manager::ACTION_VIEW_COMPLEX_CONTENT_OBJECT,
                    Manager::PARAM_CHILD_ID => $treeNode->getId(),
                    \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ACTION =>
                        \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::ACTION_ENTRY,
                    \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ENTITY_TYPE => $entityType,
                    \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ENTITY_ID => $entityId
                )
            );
        }

        return null;
    }

    /**
     * @return AssignmentServiceBridgeInterface
     */
    protected function getAssignmentServiceBridge()
    {
        return $this->getBridgeManager()->getBridgeByInterface(AssignmentServiceBridgeInterface::class);
    }

    /**
     * Returns the condition
     *
     * @param string $table_class_name
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    public function get_table_condition($table_class_name)
    {
        return null;
    }
}
