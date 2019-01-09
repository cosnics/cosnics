<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\TargetUserProgress\TargetUserProgressTable;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AutomaticNumberingService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\PanelRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Lists the users for this learning path with their progress
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserProgressComponent extends BaseReportingComponent implements TableSupport
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    protected $buttonToolbarRenderer;

    function build()
    {
        if (!$this->canViewReporting())
        {
            throw new NotAllowedException();
        }

        $panelRenderer = new PanelRenderer();
        $translator = Translation::getInstance();

        $this->addBreadcrumbs($translator);

        $html = array();
        $html[] = $this->render_header();
        $html[] = $this->renderCommonFunctionality();

        $html[] = '<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js">';
        $html[] = '</script>';

        $html[] = '<div class="row">';
        $html[] = '<div class="col-lg-8 col-md-12">';

        $html[] = $this->renderInformationPanel(
            $this->getCurrentTreeNode(),
            $this->getAutomaticNumberingService(),
            $translator,
            $panelRenderer
        );

        $html[] = '</div>';
        $html[] = '<div class="col-lg-4 col-md-12">';

        $html[] = $this->renderTargetStatistics($panelRenderer, $translator);

        $html[] = '</div>';
        $html[] = '</div>';

        $table = new TargetUserProgressTable($this);
        $table->setSearchForm($this->getSearchButtonToolbarRenderer()->getSearchForm());

        $html[] = $panelRenderer->render(
            $translator->getTranslation('UserProgress'),
            $this->getSearchButtonToolbarRenderer()->render() . $table->as_html()
        );

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Adds the breadcrumbs for this component
     *
     * @param Translation $translator
     */
    protected function addBreadcrumbs(Translation $translator)
    {
        $trail = BreadcrumbTrail::getInstance();
        $trail->add(new Breadcrumb($this->get_url(), $translator->getTranslation('UserProgressComponent')));
    }

    /**
     * Renders the information panel
     *
     * @param TreeNode $currentTreeNode
     * @param AutomaticNumberingService $automaticNumberingService
     * @param Translation $translator
     * @param PanelRenderer $panelRenderer
     *
     * @return string
     */
    protected function renderInformationPanel(
        TreeNode $currentTreeNode,
        AutomaticNumberingService $automaticNumberingService, Translation $translator, PanelRenderer $panelRenderer
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

        return $panelRenderer->renderTablePanel($translator->getTranslation('Information'), $informationValues);
    }

    /**
     * Renders the statistics for the target users
     *
     * @param PanelRenderer $panelRenderer
     * @param Translation $translator
     *
     * @return string
     */
    protected function renderTargetStatistics(PanelRenderer $panelRenderer, Translation $translator)
    {
        $trackingService = $this->getTrackingService();

        $labels = [
            $translator->getTranslation('TargetUsersWithFullAttempts'),
            $translator->getTranslation('TargetUsersWithPartialAttempts'),
            $translator->getTranslation('TargetUsersWithoutAttempts')
        ];

        $data = [
            $trackingService->countTargetUsersWithFullLearningPathAttempts(
                $this->learningPath,
                $this->getCurrentTreeNode()
            ),
            $trackingService->countTargetUsersWithPartialLearningPathAttempts(
                $this->learningPath,
                $this->getCurrentTreeNode()
            ),
            $trackingService->countTargetUsersWithoutLearningPathAttempts(
                $this->learningPath,
                $this->getCurrentTreeNode()
            )
        ];

        $panelHtml = array();
        $panelHtml[] = '<canvas id="myChart" height="135" width="270" style="margin: auto;"></canvas>';
        $panelHtml[] = '<script>';
        $panelHtml[] = 'var ctx = document.getElementById("myChart");';
        $panelHtml[] = 'var myChart = new Chart(ctx, {';
        $panelHtml[] = '    type: "doughnut",';
        $panelHtml[] = '    data: {';
        $panelHtml[] = '        labels: ["' . implode('", "', $labels) . '"],';
        $panelHtml[] = '        datasets: [{';
        $panelHtml[] = '            data: [' . implode(', ', $data) . '],';
        $panelHtml[] = '            backgroundColor: [';
        $panelHtml[] = '                    "#5cb85c",';
        $panelHtml[] = '                    "#36A2EB",';
        $panelHtml[] = '                    "#FF6384",';
        $panelHtml[] = '                ],';
        $panelHtml[] = '            hoverBackgroundColor: [';
        $panelHtml[] = '                    "#5cb85c",';
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
        $panelHtml[] = '    }';
        $panelHtml[] = '});';
        $panelHtml[] = '</script>';

        return $panelRenderer->render(
            $translator->getTranslation('OverviewUserProgress'), implode(PHP_EOL, $panelHtml)
        );
    }

    /**
     *
     * @return ButtonToolBarRenderer
     */
    protected function getSearchButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());

            $buttonToolbar->addItem(
                new Button(
                    Translation::getInstance()->getTranslation('Export', null, Utilities::COMMON_LIBRARIES),
                    new FontAwesomeGlyph('download'),
                    $this->get_url(
                        [
                            self::PARAM_ACTION => self::ACTION_EXPORT_REPORTING,
                            ReportingExporterComponent::PARAM_EXPORT => ReportingExporterComponent::EXPORT_USER_PROGRESS
                        ]
                    )
                )
            );

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    protected function getButtonToolbar(Translation $translator)
    {
        $toolbar = parent::getButtonToolbar($translator);

        $buttonGroup = new ButtonGroup();

        $translationVariable =
            $this->getCurrentTreeNode()->isRootNode() ? 'MailNotCompletedUsersRoot' : 'MailNotCompletedUsers';

        if ($this->canEditCurrentTreeNode())
        {
            $buttonGroup->addButton(
                new Button(
                    $translator->getTranslation($translationVariable),
                    new FontAwesomeGlyph('envelope'),
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_MAIL_USERS_WITH_INCOMPLETE_PROGRESS)),
                    Button::DISPLAY_ICON_AND_LABEL,
                    true
                )
            );
        }

        $toolbar->addItem($buttonGroup);

        return $toolbar;
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
        return $this->getSearchButtonToolbarRenderer()->getConditions(
            array(
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME),
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME),
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_EMAIL)
            )
        );
    }

    /**
     * Returns whether or not the progress should be shown
     *
     * @return bool
     */
    protected function showProgressInTree()
    {
        return false;
    }
}