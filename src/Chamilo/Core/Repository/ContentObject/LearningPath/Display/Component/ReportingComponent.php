<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\ChildAttempt\ChildAttemptTable;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\Progress\ProgressTable;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\UserProgress\UserProgressTable;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AutomaticNumberingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathTrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\PanelRenderer;
use Chamilo\Libraries\Format\Structure\ProgressBarRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 * Shows the progress of a user in the learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ReportingComponent extends BaseReportingComponent implements TableSupport
{

    /**
     * @return string
     */
    public function build()
    {
        $html = [parent::render_header()];

        $translator = Translation::getInstance();
        $trackingService = $this->getLearningPathTrackingService();
        $currentLearningPathTreeNode = $this->getCurrentLearningPathTreeNode();
        $automaticNumberingService = $this->getAutomaticNumberingService();
        $panelRenderer = new PanelRenderer();

        $html[] = $this->renderCommonFunctionality();

        $html[] = '<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js">';
        $html[] = '</script>';

        $html[] = '<div class="row">';

        $class = $currentLearningPathTreeNode->hasChildNodes() ? 'col-sm-8' : 'col-sm-12';

        $html[] = '<div class="' . $class . '">';

        $html[] = $this->renderInformationPanel(
            $currentLearningPathTreeNode, $automaticNumberingService, $translator, $trackingService, $panelRenderer
        );

        $html[] = '</div>';

        if ($currentLearningPathTreeNode->hasChildNodes())
        {
            $html[] = '<div class="col-sm-4">';
            $html[] =
                $this->renderProgress($translator, $trackingService, $currentLearningPathTreeNode, $panelRenderer);
            $html[] = '</div>';
        }

        $html[] = '</div>';

        if ($this->getCurrentLearningPathTreeNode()->hasChildNodes())
        {
            $table = new ProgressTable($this);
            $html[] = $panelRenderer->render($translator->getTranslation('Children'), $table->as_html());
        }

        $table = new ChildAttemptTable($this);
        $html[] = $panelRenderer->render($translator->getTranslation('Attempts'), $table->as_html());

        if ($currentLearningPathTreeNode->getContentObject() instanceof Assessment)
        {
            $html[] = $this->renderScoreChart(
                $trackingService, $translator, $panelRenderer, $this->get_root_content_object(),
                $this->getReportingUser(),
                $currentLearningPathTreeNode
            );
        }

        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath(Manager::package(), true) . 'KeyboardNavigation.js'
        );

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Renders the information panel
     *
     * @param LearningPathTreeNode $currentLearningPathTreeNode
     * @param AutomaticNumberingService $automaticNumberingService
     * @param Translation $translator
     * @param LearningPathTrackingService $trackingService
     * @param PanelRenderer $panelRenderer
     *
     * @return string
     */
    protected function renderInformationPanel(
        LearningPathTreeNode $currentLearningPathTreeNode, AutomaticNumberingService $automaticNumberingService,
        Translation $translator, LearningPathTrackingService $trackingService, PanelRenderer $panelRenderer
    )
    {
        $parentTitles = array();
        foreach ($currentLearningPathTreeNode->getParentNodes() as $parentNode)
        {
            $url = $this->get_url(array(self::PARAM_CHILD_ID => $parentNode->getId()));
            $title = $automaticNumberingService->getAutomaticNumberedTitleForLearningPathTreeNode($parentNode);
            $parentTitles[] = '<a href="' . $url . '">' . $title . '</a>';
        }

        $informationValues = [];

        $informationValues[$translator->getTranslation('Title')] =
            $automaticNumberingService->getAutomaticNumberedTitleForLearningPathTreeNode(
                $currentLearningPathTreeNode
            );

        if (!$currentLearningPathTreeNode->isRootNode())
        {
            $informationValues[$translator->getTranslation('Parents')] = implode(' >> ', $parentTitles);
        }

        $informationValues[$translator->getTranslation('User')] = $this->getReportingUser()->get_fullname();

        $informationValues[$translator->getTranslation('TotalTime')] =
            DatetimeUtilities::format_seconds_to_hours(
                $trackingService->getTotalTimeSpentInLearningPathTreeNode(
                    $this->get_root_content_object(), $this->getReportingUser(), $currentLearningPathTreeNode
                )
            );

        if ($this->getCurrentContentObject() instanceof Assessment)
        {
            $progressBarRenderer = new ProgressBarRenderer();

            $informationValues[$translator->getTranslation('AverageScore')] =
                $progressBarRenderer->render(
                    (int) $trackingService->getAverageScoreInLearningPathTreeNode(
                        $this->get_root_content_object(), $this->getReportingUser(), $currentLearningPathTreeNode
                    )
                );

            $informationValues[$translator->getTranslation('MaximumScore')] =
                $progressBarRenderer->render(
                    $trackingService->getMaximumScoreInLearningPathTreeNode(
                        $this->get_root_content_object(), $this->getReportingUser(), $currentLearningPathTreeNode
                    )
                );

            $informationValues[$translator->getTranslation('MinimumScore')] =
                $progressBarRenderer->render(
                    $trackingService->getMinimumScoreInLearningPathTreeNode(
                        $this->get_root_content_object(), $this->getReportingUser(), $currentLearningPathTreeNode
                    )
                );

            $informationValues[$translator->getTranslation('LastScore')] =
                $progressBarRenderer->render(
                    $trackingService->getLastAttemptScoreForLearningPathTreeNode(
                        $this->get_root_content_object(), $this->getReportingUser(), $currentLearningPathTreeNode
                    )
                );
        }

        return $panelRenderer->renderTablePanel($translator->getTranslation('Information'), $informationValues);
    }

    /**
     * Renders the progress doughnut chart
     *
     * @param Translation $translator
     * @param LearningPathTrackingService $trackingService
     * @param LearningPathTreeNode $currentLearningPathTreeNode
     * @param PanelRenderer $panelRenderer
     * 
     * @return string
     */
    protected function renderProgress(
        Translation $translator, LearningPathTrackingService $trackingService,
        LearningPathTreeNode $currentLearningPathTreeNode, PanelRenderer $panelRenderer
    )
    {
        $completedLabel = $translator->getTranslation('Completed');
        $notCompletedLabel = $translator->getTranslation('NotCompleted');

        $progress = $trackingService->getLearningPathProgress(
            $this->get_root_content_object(), $this->getReportingUser(), $currentLearningPathTreeNode
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
        $panelHtml[] = '         responsive: false,';
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
            $translator->getTranslation('Progress'), implode(PHP_EOL, $panelHtml)
        );
    }

    /**
     * Renders the scores for every attempt in a chart
     *
     * @param LearningPathTrackingService $learningPathTrackingService
     * @param Translation $translator
     * @param PanelRenderer $panelRenderer
     * @param LearningPath $learningPath
     * @param User $user
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return string
     */
    protected function renderScoreChart(
        LearningPathTrackingService $learningPathTrackingService, Translation $translator, PanelRenderer $panelRenderer,
        LearningPath $learningPath, User $user, LearningPathTreeNode $learningPathTreeNode
    )
    {
        $labels = $scores = [];

        $learningPathChildAttempts = $learningPathTrackingService->getLearningPathTreeNodeAttempts(
            $learningPath, $user, $learningPathTreeNode
        );

        foreach ($learningPathChildAttempts as $learningPathChildAttempt)
        {
            $labels[] = DatetimeUtilities::format_locale_date(null, $learningPathChildAttempt->get_start_time());
            $scores[] = (int) $learningPathChildAttempt->get_score();
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
