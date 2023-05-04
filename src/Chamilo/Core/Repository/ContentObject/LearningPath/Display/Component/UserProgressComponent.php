<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\UserProgressTableRenderer;
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
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Lists the users for this learning path with their progress
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserProgressComponent extends BaseReportingComponent
{

    /**
     * @var ButtonToolBarRenderer
     */
    protected $buttonToolbarRenderer;

    protected function addBreadcrumbs()
    {
        $trail = BreadcrumbTrail::getInstance();
        $trail->add(
            new Breadcrumb(
                $this->get_url(), $this->getTranslator()->trans('UserProgressComponent', [], Manager::CONTEXT)
            )
        );
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\LearningPath\Exception\TreeNodeNotFoundException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    public function build()
    {
        if (!$this->canEditCurrentTreeNode())
        {
            throw new NotAllowedException();
        }

        $panelRenderer = new PanelRenderer();

        $this->addBreadcrumbs();

        $html = [];
        $html[] = $this->render_header();
        $html[] = $this->renderCommonFunctionality();

        $html[] = '<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js">';
        $html[] = '</script>';

        $html[] = '<div class="row">';
        $html[] = '<div class="col-lg-8 col-md-12">';

        $html[] = $this->renderInformationPanel(
            $this->getCurrentTreeNode(), $this->getAutomaticNumberingService(), $panelRenderer
        );

        $html[] = '</div>';
        $html[] = '<div class="col-lg-4 col-md-12">';

        $html[] = $this->renderTargetStatistics($panelRenderer);

        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = $panelRenderer->render(
            $this->getTranslator()->trans('UserProgress', [], Manager::CONTEXT),
            $this->getSearchButtonToolbarRenderer()->render() . $this->renderTable()
        );

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    protected function cleanupOrderBy(?OrderBy $orderBy = null): ?OrderBy
    {
        if ($orderBy instanceof OrderBy)
        {
            $firstOrderProperty = $orderBy->getFirst();

            if ($firstOrderProperty->getConditionVariable() instanceof StaticConditionVariable)
            {
                $value = $firstOrderProperty->getConditionVariable()->getValue();

                if (in_array($value, ['progress', 'completed', 'started']))
                {
                    $firstOrderProperty->getConditionVariable()->setValue('nodes_completed');
                    $firstOrderProperty->setDirection(
                        $value == 'started' ? $firstOrderProperty->getDirection() :
                            ($firstOrderProperty->getDirection() == SORT_ASC ? SORT_DESC : SORT_ASC)
                    );
                }
            }
        }

        return $orderBy;
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\LearningPath\Exception\TreeNodeNotFoundException
     */
    protected function getButtonToolbar()
    {
        $toolbar = parent::getButtonToolbar();
        $translator = $this->getTranslator();

        $buttonGroup = new ButtonGroup();

        $translationVariable =
            $this->getCurrentTreeNode()->isRootNode() ? 'MailNotCompletedUsersRoot' : 'MailNotCompletedUsers';

        $buttonGroup->addButton(
            new Button(
                $translator->trans($translationVariable, [], Manager::CONTEXT), new FontAwesomeGlyph('envelope'),
                $this->get_url([self::PARAM_ACTION => self::ACTION_MAIL_USERS_WITH_INCOMPLETE_PROGRESS]),
                Button::DISPLAY_ICON_AND_LABEL,
                $translator->trans('ConfirmChosenAction', [], StringUtilities::LIBRARIES)
            )
        );

        $toolbar->addItem($buttonGroup);

        return $toolbar;
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @return ButtonToolBarRenderer
     */
    protected function getSearchButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());

            $buttonToolbar->addItem(
                new Button(
                    $this->getTranslator()->trans('Export', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('download'), $this->get_url(
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

    /**
     * @throws \Exception
     */
    public function getUserProgressCondition(): ?AndCondition
    {
        return $this->getSearchButtonToolbarRenderer()->getConditions(
            [
                new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME),
                new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME),
                new PropertyConditionVariable(User::class, User::PROPERTY_EMAIL)
            ]
        );
    }

    public function getUserProgressTableRenderer(): UserProgressTableRenderer
    {
        return $this->getService(UserProgressTableRenderer::class);
    }

    /**
     * Renders the information panel
     *
     * @param TreeNode $currentTreeNode
     * @param AutomaticNumberingService $automaticNumberingService
     * @param PanelRenderer $panelRenderer
     *
     * @return string
     */
    protected function renderInformationPanel(
        TreeNode $currentTreeNode, AutomaticNumberingService $automaticNumberingService, PanelRenderer $panelRenderer
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

        return $panelRenderer->renderTablePanel(
            $informationValues, $translator->trans('Information', [], Manager::CONTEXT)
        );
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\LearningPath\Exception\TreeNodeNotFoundException
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Exception
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems = $this->getTrackingService()->countLearningPathAttemptsWithUsers(
            $this->get_root_content_object(), $this->getCurrentTreeNode(), $this->getUserProgressCondition()
        );

        $userProgressTableRenderer = $this->getUserProgressTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $userProgressTableRenderer->getParameterNames(), $userProgressTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $attempts = $this->getTrackingService()->getLearningPathAttemptsWithUser(
            $this->get_root_content_object(), $this->getCurrentTreeNode(), $this->getUserProgressCondition(),
            $tableParameterValues->getOffset(), $tableParameterValues->getNumberOfItemsPerPage(),
            $this->cleanupOrderBy($userProgressTableRenderer->determineOrderBy($tableParameterValues))
        );

        return $userProgressTableRenderer->legacyRender($this, $tableParameterValues, $attempts);
    }

    /**
     * @throws \Chamilo\Core\Repository\ContentObject\LearningPath\Exception\TreeNodeNotFoundException
     */
    protected function renderTargetStatistics(PanelRenderer $panelRenderer): string
    {
        $trackingService = $this->getTrackingService();
        $translator = $this->getTranslator();

        $labels = [
            $translator->trans('TargetUsersWithFullAttempts', [], Manager::CONTEXT),
            $translator->trans('TargetUsersWithPartialAttempts', [], Manager::CONTEXT),
            $translator->trans('TargetUsersWithoutAttempts', [], Manager::CONTEXT)
        ];

        $data = [
            $trackingService->countTargetUsersWithFullLearningPathAttempts(
                $this->learningPath, $this->getCurrentTreeNode()
            ),
            $trackingService->countTargetUsersWithPartialLearningPathAttempts(
                $this->learningPath, $this->getCurrentTreeNode()
            ),
            $trackingService->countTargetUsersWithoutLearningPathAttempts(
                $this->learningPath, $this->getCurrentTreeNode()
            )
        ];

        $panelHtml = [];
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
            $translator->trans('OverviewUserProgress', [], Manager::CONTEXT), implode(PHP_EOL, $panelHtml)
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