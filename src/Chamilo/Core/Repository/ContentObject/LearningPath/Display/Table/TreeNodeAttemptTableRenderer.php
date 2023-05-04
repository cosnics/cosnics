<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table;

use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AutomaticNumberingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ProgressBarRenderer;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableParameterValues;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TreeNodeAttemptTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport
{
    public const PROPERTY_LAST_START_TIME = 'last_start_time';
    public const PROPERTY_SCORE = 'score';
    public const PROPERTY_STATUS = 'status';
    public const PROPERTY_TIME = 'time';

    /**
     * @deprecated Temporary solution to allow rendering of DI-based tables in a non-DI context
     * @var ?\Chamilo\Libraries\Architecture\Application\Application
     */
    protected ?Application $application = null;

    protected AutomaticNumberingService $automaticNumberingService;

    protected DatetimeUtilities $datetimeUtilities;

    protected ProgressBarRenderer $progressBarRenderer;

    protected TrackingService $trackingService;

    protected User $user;

    public function __construct(
        ProgressBarRenderer $progressBarRenderer, DatetimeUtilities $datetimeUtilities,
        AutomaticNumberingService $automaticNumberingService, TrackingService $trackingService, User $user,
        Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager
    )
    {
        $this->user = $user;
        $this->trackingService = $trackingService;
        $this->automaticNumberingService = $automaticNumberingService;
        $this->datetimeUtilities = $datetimeUtilities;
        $this->progressBarRenderer = $progressBarRenderer;

        parent::__construct($translator, $urlGenerator, $htmlTableRenderer, $pager);
    }

    public function getAutomaticNumberingService(): AutomaticNumberingService
    {
        return $this->automaticNumberingService;
    }

    protected function getCurrentTreeNode(): ?TreeNode
    {
        return $this->application->getCurrentTreeNode();
    }

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    public function getProgressBarRenderer(): ProgressBarRenderer
    {
        return $this->progressBarRenderer;
    }

    protected function getReportingUser()
    {
        return $this->application->getReportingUser();
    }

    public function getTrackingService(): TrackingService
    {
        return $this->trackingService;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    protected function initializeColumns()
    {
        $translator = $this->getTranslator();

        $this->addColumn(
            new StaticTableColumn(
                self::PROPERTY_LAST_START_TIME, $translator->trans('LastStartTime', [], Manager::CONTEXT)
            )
        );
        $this->addColumn(
            new StaticTableColumn(self::PROPERTY_STATUS, $translator->trans('Status', [], Manager::CONTEXT))
        );

        if ($this->getCurrentTreeNode()->supportsScore())
        {
            $this->addColumn(
                new StaticTableColumn(self::PROPERTY_SCORE, $translator->trans('Score', [], Manager::CONTEXT))
            );
        }

        $this->addColumn(new StaticTableColumn(self::PROPERTY_TIME, $translator->trans('Time', [], Manager::CONTEXT)));
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     * @deprecated Temporary solution to allow rendering of DI-based tables in a non-DI context
     */
    public function legacyRender(
        Application $application, TableParameterValues $parameterValues, ArrayCollection $tableData,
        ?string $tableName = null
    ): string
    {
        $this->application = $application;

        return parent::render($parameterValues, $tableData, $tableName); // TODO: Change the autogenerated stub
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt $treeNodeAttempt
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $treeNodeAttempt): string
    {
        $translator = $this->getTranslator();
        $datetimeUtiltities = $this->getDatetimeUtilities();

        switch ($column->get_name())
        {
            case self::PROPERTY_LAST_START_TIME:
                return $datetimeUtiltities->formatLocaleDate(null, $treeNodeAttempt->get_start_time());
            case self::PROPERTY_STATUS:
                return $translator->trans(
                    $treeNodeAttempt->isCompleted() ? 'Completed' : 'Incomplete', [], Manager::CONTEXT
                );
            case self::PROPERTY_SCORE:
                return $this->getProgressBarRenderer()->render(
                    (int) $treeNodeAttempt->get_score(), ProgressBarRenderer::MODE_SUCCESS
                );

            case self::PROPERTY_TIME:
                return $datetimeUtiltities->formatSecondsToHours($treeNodeAttempt->get_total_time());
        }

        return parent::renderCell($column, $resultPosition, $treeNodeAttempt);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt $treeNodeAttempt
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $treeNodeAttempt): string
    {
        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();

        $toolbar = new Toolbar();

        if ($this->getCurrentTreeNode()->getContentObject() instanceof Assessment)
        {
            $assessmentResultViewerUrl = $urlGenerator->fromRequest(
                [
                    Manager::PARAM_ACTION => Manager::ACTION_VIEW_ASSESSMENT_RESULT,
                    Manager::PARAM_CHILD_ID => $this->getCurrentTreeNode()->getId(),
                    Manager::PARAM_ITEM_ATTEMPT_ID => $treeNodeAttempt->getId()
                ]
            );

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('ViewAssessmentResult', [], Manager::CONTEXT), new FontAwesomeGlyph('chart-pie'),
                    $assessmentResultViewerUrl, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($this->application->is_allowed_to_edit_attempt_data() &&
            $this->getTrackingService()->canDeleteLearningPathAttemptData(
                $this->getUser(), $this->getReportingUser()
            ))
        {
            $delete_url = $urlGenerator->fromRequest(
                [
                    Manager::PARAM_ACTION => Manager::ACTION_DELETE_TREE_NODE_ATTEMPT,
                    Manager::PARAM_CHILD_ID => $this->getCurrentTreeNode()->getId(),
                    Manager::PARAM_ITEM_ATTEMPT_ID => $treeNodeAttempt->getId()
                ]
            );

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('DeleteAttempt', [], Manager::CONTEXT), new FontAwesomeGlyph('times'),
                    $delete_url, ToolbarItem::DISPLAY_ICON, true
                )
            );
        }

        return $toolbar->render();
    }
}
