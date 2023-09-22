<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component\DeleteAttemptsForTreeNodeComponent;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AutomaticNumberingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ProgressBarRenderer;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\ListTableRenderer;
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
class TreeNodeProgressTableRenderer extends ListTableRenderer implements TableRowActionsSupport
{
    public const PROPERTY_SCORE = 'score';
    public const PROPERTY_STATUS = 'status';
    public const PROPERTY_TIME = 'time';
    public const PROPERTY_TITLE = 'title';
    public const PROPERTY_TYPE = 'type';

    /**
     * @deprecated Temporary solution to allow rendering of DI-based tables in a non-DI context
     * @var ?\Chamilo\Libraries\Architecture\Application\Application
     */
    protected ?Application $application = null;

    protected AutomaticNumberingService $automaticNumberingService;

    protected DatetimeUtilities $datetimeUtilities;

    protected TrackingService $trackingService;

    protected User $user;

    public function __construct(
        DatetimeUtilities $datetimeUtilities, AutomaticNumberingService $automaticNumberingService,
        TrackingService $trackingService, User $user, Translator $translator, UrlGenerator $urlGenerator,
        ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        $this->user = $user;
        $this->trackingService = $trackingService;
        $this->automaticNumberingService = $automaticNumberingService;
        $this->datetimeUtilities = $datetimeUtilities;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );
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

    protected function getLearningPath(): ?LearningPath
    {
        return $this->application->get_root_content_object();
    }

    protected function getReportingUrl($userId): string
    {
        return $this->getUrlGenerator()->fromRequest(
            [
                Manager::PARAM_ACTION => Manager::ACTION_REPORTING,
                Manager::PARAM_REPORTING_USER_ID => $userId
            ]
        );
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

    protected function initializeColumns(): void
    {
        $translator = $this->getTranslator();

        $this->addColumn(new StaticTableColumn(self::PROPERTY_TYPE, $translator->trans('Type', [], Manager::CONTEXT)));
        $this->addColumn(
            new StaticTableColumn(self::PROPERTY_TITLE, $translator->trans('Title', [], Manager::CONTEXT))
        );
        $this->addColumn(
            new StaticTableColumn(self::PROPERTY_STATUS, $translator->trans('Status', [], Manager::CONTEXT))
        );
        $this->addColumn(
            new StaticTableColumn(self::PROPERTY_SCORE, $translator->trans('Score', [], Manager::CONTEXT))
        );
        $this->addColumn(new StaticTableColumn(self::PROPERTY_TIME, $translator->trans('Time', [], Manager::CONTEXT)));
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
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
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $treeNode): string
    {
        $content_object = $treeNode->getContentObject();

        $learningPath = $this->getLearningPath();
        $user = $this->getReportingUser();
        $trackingService = $this->getTrackingService();
        $automaticNumberingService = $this->getAutomaticNumberingService();
        $translator = $this->getTranslator();

        switch ($column->get_name())
        {
            case self::PROPERTY_TYPE:
                return $content_object->get_icon_image();
            case self::PROPERTY_TITLE:
                return '<a href="' . $this->getReportingUrl($treeNode) . '">' .
                    $automaticNumberingService->getAutomaticNumberedTitleForTreeNode($treeNode) . '</a>';
            case self::PROPERTY_STATUS:
                return $trackingService->isTreeNodeCompleted(
                    $learningPath, $user, $treeNode
                ) ? $translator->trans('Completed', [], Manager::CONTEXT) :
                    $translator->trans('Incomplete', [], Manager::CONTEXT);
            case self::PROPERTY_SCORE:
                if (!$treeNode->supportsScore())
                {
                    return '';
                }

                $progressBarRenderer = new ProgressBarRenderer();
                $averageScore = $trackingService->getAverageScoreInTreeNode(
                    $learningPath, $user, $treeNode
                );

                return !is_null($averageScore) ? $progressBarRenderer->render((int) $averageScore) : '';
            case self::PROPERTY_TIME:
                $totalTimeSpent = $trackingService->getTotalTimeSpentInTreeNode(
                    $learningPath, $user, $treeNode
                );

                return $this->getDatetimeUtilities()->formatSecondsToHours($totalTimeSpent);
        }

        return '';
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     */
    protected function renderIdentifierCell($treeNode): string
    {
        return (string) $treeNode->getId();
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $treeNode): string
    {
        $translator = $this->getTranslator();
        $learningPath = $this->getLearningPath();
        $reportingUser = $this->getReportingUser();
        $trackingService = $this->getTrackingService();

        $toolbar = new Toolbar();

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Reporting', [], Manager::CONTEXT), new FontAwesomeGlyph('chart-bar'),
                $this->getReportingUrl($treeNode), ToolbarItem::DISPLAY_ICON
            )
        );

        if ($trackingService->hasTreeNodeAttempts(
            $learningPath, $reportingUser, $treeNode
        ))
        {
            if ($this->application->is_allowed_to_edit_attempt_data() &&
                $trackingService->canDeleteLearningPathAttemptData($this->getUser(), $reportingUser))
            {
                $delete_url = $this->application->get_url(
                    [
                        Manager::PARAM_ACTION => Manager::ACTION_DELETE_ATTEMPTS_FOR_TREE_NODE,
                        Manager::PARAM_CHILD_ID => $treeNode->getId(),
                        DeleteAttemptsForTreeNodeComponent::PARAM_SOURCE => Manager::ACTION_REPORTING
                    ]
                );

                $toolbar->add_item(
                    new ToolbarItem(
                        $translator->trans('DeleteAttempt', [], Manager::CONTEXT), new FontAwesomeGlyph('times'),
                        $delete_url, ToolbarItem::DISPLAY_ICON, true
                    )
                );
            }
        }

        return $toolbar->render();
    }
}
