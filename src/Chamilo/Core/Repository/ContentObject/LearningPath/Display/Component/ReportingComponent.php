<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\ChildAttempt\ChildAttemptTable;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\Progress\ProgressTable;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
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

        $html[] = '<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js">';
        $html[] = '</script>';

        $html[] = '<div class="row">';

        $parentTitles = array();
        foreach ($currentLearningPathTreeNode->getParentNodes() as $parentNode)
        {
            $url = $this->get_url(array(self::PARAM_CHILD_ID => $parentNode->getId()));
            $parentTitles[] = '<a href="' . $url . '">' . $parentNode->getContentObject()->get_title() . '</a>';
        }

        $class = $currentLearningPathTreeNode->hasChildNodes() ? 'col-sm-8' : 'col-sm-12';

        $html[] = '<div class="' . $class . '">';
        $html[] = '<div class="panel panel-default">';
        $html[] = '<div class="panel-heading">';
        $html[] = '<h5 class="panel-title">' . $translator->getTranslation('Information') . '</h5>';
        $html[] = '</div>';
        $html[] = '<table class="table table-bordered">';
        $html[] = '<tr>';
        $html[] = '<td width="25%"><strong>Title</strong></td>';
        $html[] = '<td>' . $this->getCurrentContentObject()->get_title() . '</td>';
        $html[] = '</tr>';

        if (!$currentLearningPathTreeNode->isRootNode())
        {
            $html[] = '<tr>';
            $html[] = '<td width="25%"><strong>Parents</strong></td>';
            $html[] = '<td>' . implode(' >> ', $parentTitles) . '</td>';
            $html[] = '</tr>';
        }

        $html[] = '<tr>';
        $html[] = '<td width="25%"><strong>User</strong></td>';
        $html[] = '<td>' . $this->getUser()->get_fullname() . '</td>';
        $html[] = '</tr>';
        $html[] = '<tr>';
        $html[] = '<td width="25%"><strong>TotalTime</strong></td>';
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
            $html[] = '<tr>';
            $html[] = '<td width="25%"><strong>AverageScore</strong></td>';
            $html[] = '<td>';

            $html[] = $trackingService->getAverageScoreInLearningPathTreeNode(
                $this->get_root_content_object(), $this->getUser(), $currentLearningPathTreeNode
            );

            $html[] = '%</td>';
            $html[] = '</tr>';
            $html[] = '<tr>';
            $html[] = '<td width="25%"><strong>MaximumScore</strong></td>';
            $html[] = '<td>';

            $html[] = $trackingService->getMaximumScoreInLearningPathTreeNode(
                $this->get_root_content_object(), $this->getUser(), $currentLearningPathTreeNode
            );

            $html[] = '%</td>';
            $html[] = '</tr>';

            $html[] = '<tr>';
            $html[] = '<td width="25%"><strong>MinimumScore</strong></td>';
            $html[] = '<td>';

            $html[] = $trackingService->getMinimumScoreInLearningPathTreeNode(
                $this->get_root_content_object(), $this->getUser(), $currentLearningPathTreeNode
            );

            $html[] = '%</td>';
            $html[] = '</tr>';

            $html[] = '<tr>';
            $html[] = '<td width="25%"><strong>LastScore</strong></td>';
            $html[] = '<td>';

            $html[] = $trackingService->getLastAttemptScoreForLearningPathTreeNode(
                $this->get_root_content_object(), $this->getUser(), $currentLearningPathTreeNode
            );

            $html[] = '%</td>';
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

            $html[] = '<canvas id="myChart" width="270" height="135"></canvas>';
            $html[] = '<script>';
            $html[] = 'var ctx = document.getElementById("myChart");';
            $html[] = 'var myChart = new Chart(ctx, {';
            $html[] = '    type: "doughnut",';
            $html[] = '    data: {';
            $html[] = '            labels: ["' . $completedLabel . '", "' . $notCompletedLabel . '"],';
            $html[] = '        datasets: [{';
            $html[] = '                label: "# of Votes",';
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
            $html[] = '         animation: { animateScale: true },';
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

        $html[] = $this->render_footer();

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
