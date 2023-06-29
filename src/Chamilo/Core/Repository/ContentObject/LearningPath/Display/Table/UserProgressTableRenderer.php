<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
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
use Chamilo\Libraries\Format\Table\Column\SortableStaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordListTableRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableParameterValues;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\ContentObject\Glossary\Display\Component\Renderer\Type
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserProgressTableRenderer extends RecordListTableRenderer implements TableRowActionsSupport
{
    public const DEFAULT_ORDER_COLUMN_INDEX = 3;

    public const PROPERTY_COMPLETED = 'completed';
    public const PROPERTY_PROGRESS = 'progress';
    public const PROPERTY_STARTED = 'started';

    /**
     * @deprecated Temporary solution to allow rendering of DI-based tables in a non-DI context
     * @var ?\Chamilo\Libraries\Architecture\Application\Application
     */
    protected ?Application $application = null;

    protected ConfigurationConsulter $configurationConsulter;

    protected TrackingService $trackingService;

    protected User $user;

    public function __construct(
        TrackingService $trackingService, ConfigurationConsulter $configurationConsulter, User $user,
        Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        $this->user = $user;
        $this->trackingService = $trackingService;
        $this->configurationConsulter = $configurationConsulter;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    protected function getCurrentTreeNode(): ?TreeNode
    {
        return $this->application->getCurrentTreeNode();
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

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_LASTNAME)
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_FIRSTNAME)
        );

        $showEmail = $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\User', 'show_email_addresses']);

        if ($showEmail)
        {
            $this->addColumn(
                $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_EMAIL)
            );
        }

        $this->addColumn(
            new SortableStaticTableColumn(self::PROPERTY_PROGRESS, $translator->trans('Progress', [], Manager::CONTEXT))
        );
        $this->addColumn(
            new SortableStaticTableColumn(
                self::PROPERTY_COMPLETED, $translator->trans('Completed', [], Manager::CONTEXT)
            )
        );
        $this->addColumn(
            new SortableStaticTableColumn(self::PROPERTY_STARTED, $translator->trans('Started', [], Manager::CONTEXT))
        );
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

    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $record): string
    {
        switch ($column->get_name())
        {
            case self::PROPERTY_PROGRESS:
                $trackingService = $this->getTrackingService();
                $learningPath = $this->getLearningPath();
                $currentTreeNode = $this->getCurrentTreeNode();

                $user = new User();
                $user->setId($record[TreeNodeAttempt::PROPERTY_USER_ID]);

                $progress = $trackingService->getLearningPathProgress(
                    $learningPath, $user, $currentTreeNode
                );

                $progressBarRenderer = new ProgressBarRenderer();

                return $progressBarRenderer->render($progress);
            case self::PROPERTY_COMPLETED:
                $numberOfNodes = $record['nodes_completed'];
                $currentTreeNode = $this->getCurrentTreeNode();

                if ($numberOfNodes >= count($currentTreeNode->getDescendantNodes()) + 1)
                {
                    $glyph = new FontAwesomeGlyph(
                        'check-circle', ['text-success'], null, 'fas'
                    );

                    return $glyph->render();
                }

                return '';
            case self::PROPERTY_STARTED:
                $numberOfNodes = $record['nodes_completed'];
                if ($numberOfNodes > 0)
                {
                    $glyph = new FontAwesomeGlyph(
                        'check-circle', ['text-success'], null, 'fas'
                    );

                    return $glyph->render();
                }

                return '';

            case User::PROPERTY_FIRSTNAME:
            case User::PROPERTY_LASTNAME:
                return '<a href="' . $this->getReportingUrl($record['user_id']) . '">' .
                    parent::renderCell($column, $resultPosition, $record) . '</a>';
        }

        return parent::renderCell($column, $resultPosition, $record);
    }

    /**
     * @throws \ReflectionException
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $record): string
    {
        $learningPath = $this->getLearningPath();
        $trackingService = $this->getTrackingService();
        $translator = $this->getTranslator();

        $reportingUser = new User();
        $reportingUser->setId($record[TreeNodeAttempt::PROPERTY_USER_ID]);

        $toolbar = new Toolbar();

        $reportingUrl = $this->getReportingUrl($record['user_id']);

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Reporting', [], Manager::CONTEXT), new FontAwesomeGlyph('chart-pie'), $reportingUrl,
                ToolbarItem::DISPLAY_ICON
            )
        );

        if ($trackingService->hasTreeNodeAttempts(
            $learningPath, $reportingUser, $this->getCurrentTreeNode()
        ))
        {
            if ($this->application->is_allowed_to_edit_attempt_data() &&
                $trackingService->canDeleteLearningPathAttemptData($this->getUser(), $reportingUser))
            {
                $delete_url = $this->getUrlGenerator()->fromRequest(
                    [
                        Manager::PARAM_ACTION => Manager::ACTION_DELETE_ATTEMPTS_FOR_TREE_NODE,
                        Manager::PARAM_REPORTING_USER_ID => $record['user_id']
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
