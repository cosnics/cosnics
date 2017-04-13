<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\ChildAttempt\ChildAttemptTable;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\Progress\ProgressTable;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathTrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\Path;
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
class ReportingComponent extends TabComponent implements TableSupport
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

        $html[] = '<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js">';
        $html[] = '</script>';

        $html[] = '<div class="row">';

        $parentTitles = array();
        foreach ($currentLearningPathTreeNode->getParentNodes() as $parentNode)
        {
            $url = $this->get_url(array(self::PARAM_CHILD_ID => $parentNode->getId()));
            $title = $automaticNumberingService->getAutomaticNumberedTitleForLearningPathTreeNode($parentNode);
            $parentTitles[] = '<a href="' . $url . '">' . $title . '</a>';
        }

        $class = $currentLearningPathTreeNode->hasChildNodes() ? 'col-sm-8' : 'col-sm-12';

        $html[] = '<div class="' . $class . '">';
        $html[] = '<div class="panel panel-default">';
        $html[] = '<div class="panel-heading">';
        $html[] = '<h5 class="panel-title">' . $translator->getTranslation('Information') . '</h5>';
        $html[] = '</div>';
        $html[] = '<table class="table table-bordered">';
        $html[] = '<tr>';
        $html[] = '<td width="25%"><strong>' . $translator->getTranslation('Title') . '</strong></td>';
        $html[] = '<td>';

        $html[] = $automaticNumberingService->getAutomaticNumberedTitleForLearningPathTreeNode(
            $currentLearningPathTreeNode
        );

        $html[] = '</td>';
        $html[] = '</tr>';

        if (!$currentLearningPathTreeNode->isRootNode())
        {
            $html[] = '<tr>';
            $html[] = '<td width="25%"><strong>' . $translator->getTranslation('Parents') . '</strong></td>';
            $html[] = '<td>' . implode(' >> ', $parentTitles) . '</td>';
            $html[] = '</tr>';
        }

        $html[] = '<tr>';
        $html[] = '<td width="25%"><strong>' . $translator->getTranslation('User') . '</strong></td>';
        $html[] = '<td>' . $this->getUser()->get_fullname() . '</td>';
        $html[] = '</tr>';
        $html[] = '<tr>';
        $html[] = '<td width="25%"><strong>' . $translator->getTranslation('TotalTime') . '</strong></td>';
        $html[] = '<td>';

        $html[] = DatetimeUtilities::format_seconds_to_hours(
            $trackingService->getTotalTimeSpentInLearningPathTreeNode(
                $this->get_root_content_object(), $this->getUser(), $currentLearningPathTreeNode
            )
        );

        $html[] = '</td>';
        $html[] = '</tr>';

        if ($this->getCurrentContentObject() instanceof Assessment)
        {
            $progressBarRenderer = new ProgressBarRenderer();

            $html[] = '<tr>';
            $html[] = '<td width="25%"><strong>' . $translator->getTranslation('AverageScore') . '</strong></td>';
            $html[] = '<td>';

            $html[] = $progressBarRenderer->render(
                (int) $trackingService->getAverageScoreInLearningPathTreeNode(
                    $this->get_root_content_object(), $this->getUser(), $currentLearningPathTreeNode
                )
            );

            $html[] = '</td>';
            $html[] = '</tr>';
            $html[] = '<tr>';
            $html[] = '<td width="25%"><strong>' . $translator->getTranslation('MaximumScore') . '</strong></td>';
            $html[] = '<td>';

            $html[] = $progressBarRenderer->render(
                $trackingService->getMaximumScoreInLearningPathTreeNode(
                    $this->get_root_content_object(), $this->getUser(), $currentLearningPathTreeNode
                )
            );

            $html[] = '</td>';
            $html[] = '</tr>';

            $html[] = '<tr>';
            $html[] = '<td width="25%"><strong>' . $translator->getTranslation('MinimumScore') . '</strong></td>';
            $html[] = '<td>';

            $html[] = $progressBarRenderer->render(
                $trackingService->getMinimumScoreInLearningPathTreeNode(
                    $this->get_root_content_object(), $this->getUser(), $currentLearningPathTreeNode
                )
            );

            $html[] = '</td>';
            $html[] = '</tr>';

            $html[] = '<tr>';
            $html[] = '<td width="25%"><strong>' . $translator->getTranslation('LastScore') . '</strong></td>';
            $html[] = '<td>';

            $html[] = $progressBarRenderer->render(
                $trackingService->getLastAttemptScoreForLearningPathTreeNode(
                    $this->get_root_content_object(), $this->getUser(), $currentLearningPathTreeNode
                )
            );

            $html[] = '</td>';
            $html[] = '</tr>';
        }

        $html[] = '</table>';
        $html[] = '</div>';
        $html[] = '</div>';

        if ($currentLearningPathTreeNode->hasChildNodes())
        {
            $completedLabel = $translator->getTranslation('Completed');
            $notCompletedLabel = $translator->getTranslation('NotCompleted');

            $progress = $trackingService->getLearningPathProgress(
                $this->get_root_content_object(), $this->getUser(), $currentLearningPathTreeNode
            );

            $notCompleted = 100 - $progress;

            $html[] = '<div class="col-sm-4">';

            $html[] = '<div class="panel panel-default">';
            $html[] = '<div class="panel-heading">';
            $html[] = '<h5 class="panel-title">' . $translator->getTranslation('Progress') . '</h5>';
            $html[] = '</div>';
            $html[] = '<div class="panel-body">';

            $html[] = '<canvas id="myChart" width="270" height="135" style="margin: auto;"></canvas>';
            $html[] = '<script>';
            $html[] = 'var ctx = document.getElementById("myChart");';
            $html[] = 'var myChart = new Chart(ctx, {';
            $html[] = '    type: "doughnut",';
            $html[] = '    data: {';
            $html[] = '        labels: ["' . $completedLabel . '", "' . $notCompletedLabel . '"],';
            $html[] = '        datasets: [{';
            $html[] = '            data: [' . $progress . ',' . $notCompleted . '],';
            $html[] = '            backgroundColor: [';
            $html[] = '                    "#36A2EB",';
            $html[] = '                    "#FF6384",';
            $html[] = '                ],';
            $html[] = '            hoverBackgroundColor: [';
            $html[] = '                    "#36A2EB",';
            $html[] = '                    "#FF6384",';
            $html[] = '                ],';
            $html[] = '            borderWidth: 1';
            $html[] = '        }]';
            $html[] = '    },';
            $html[] = '    options: {';
            $html[] = '         legend: {';
            $html[] = '             onClick: null';
            $html[] = '         },';
            $html[] = '         responsive: false,';
            $html[] = '         animation: { animateScale: true },';
            $html[] = '         legend: { position: "right" },';
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

            $html[] = '</div>';
            $html[] = '</div>';
            $html[] = '</div>';
        }

        $html[] = '</div>';

        if ($this->getCurrentLearningPathTreeNode()->hasChildNodes())
        {
            $table = new ProgressTable($this);

            $html[] = '<div class="panel panel-default">';
            $html[] = '<div class="panel-heading">';
            $html[] = '<h5 class="panel-title">' . $translator->getTranslation('Children') . '</h5>';
            $html[] = '</div>';
            $html[] = '<div class="panel-body">';
            $html[] = $table->as_html();
            $html[] = '</div>';
            $html[] = '</div>';
        }

        $table = new ChildAttemptTable($this);

        $html[] = '<div class="panel panel-default">';
        $html[] = '<div class="panel-heading">';
        $html[] = '<h5 class="panel-title">' . $translator->getTranslation('Attempts') . '</h5>';
        $html[] = '</div>';
        $html[] = '<div class="panel-body">';
        $html[] = $table->as_html();
        $html[] = '</div>';
        $html[] = '</div>';

        if ($currentLearningPathTreeNode->getContentObject() instanceof Assessment)
        {
            $html[] = $this->renderScoreChart(
                $trackingService, $translator, $this->get_root_content_object(), $this->getUser(),
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
     * Renders the scores for every attempt in a chart
     *
     * @param LearningPathTrackingService $learningPathTrackingService
     * @param Translation $translator
     * @param LearningPath $learningPath
     * @param User $user
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return string
     */
    protected function renderScoreChart(
        LearningPathTrackingService $learningPathTrackingService, Translation $translator,
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

        $html[] = '<div class="panel panel-default">';
        $html[] = '<div class="panel-heading">';
        $html[] = '<h5 class="panel-title">' . $translator->getTranslation('Scores') . '</h5>';
        $html[] = '</div>';
        $html[] = '<div class="panel-body">';

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

        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
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
