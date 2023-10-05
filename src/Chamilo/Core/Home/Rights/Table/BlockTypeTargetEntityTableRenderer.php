<?php
namespace Chamilo\Core\Home\Rights\Table;

use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Home\Rights\Manager;
use Chamilo\Core\Home\Rights\Storage\DataClass\BlockTypeTargetEntity;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordListTableRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Home\Rights\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BlockTypeTargetEntityTableRenderer extends RecordListTableRenderer implements TableRowActionsSupport
{

    protected GroupService $groupService;

    protected UserService $userService;

    public function __construct(
        Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory, GroupService $groupService,
        UserService $userService
    )
    {
        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );

        $this->groupService = $groupService;
        $this->userService = $userService;
    }

    public function getGroupService(): GroupService
    {
        return $this->groupService;
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }

    protected function initializeColumns(): void
    {
        $translator = $this->getTranslator();

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                BlockTypeTargetEntity::class, BlockTypeTargetEntity::PROPERTY_BLOCK_TYPE
            )
        );

        $this->addColumn(
            new StaticTableColumn(
                'target_entities', $translator->trans('TargetEntities', [], Manager::CONTEXT)
            )
        );
    }

    /**
     * @param string[]|string[][] $learningPathChildAttempt
     */
    public function renderCell(TableColumn $column, TableResultPosition $resultPosition, $learningPathChildAttempt
    ): string
    {
        if ($column->get_name() == 'target_entities')
        {
            return $this->renderTargetEntities((array) $learningPathChildAttempt['target_entities']);
        }

        return parent::renderCell($column, $resultPosition, $learningPathChildAttempt);
    }

    public function renderTableRowActions(TableResultPosition $resultPosition, $result): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        $saveUrl = $urlGenerator->fromParameters([
            Application::PARAM_CONTEXT => Manager::CONTEXT,
            Application::PARAM_ACTION => Manager::ACTION_SET_BLOCK_TYPE_TARGET_ENTITIES,
            Manager::PARAM_BLOCK_TYPE => $result['block_type']
        ]);

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('SetBlockTypeTargetEntitiesComponent', [], Manager::CONTEXT),
                new FontAwesomeGlyph('users', [], null, 'fas'), $saveUrl, ToolbarItem::DISPLAY_ICON
            )
        );

        return $toolbar->render();
    }

    protected function renderTargetEntities(array $targetEntities = []): string
    {
        $translator = $this->getTranslator();

        if (empty($targetEntities))
        {
            return $translator->trans('Everybody', [], StringUtilities::LIBRARIES);
        }

        $targetEntitiesHtml = [];

        $targetEntitiesHtml[] = '<select>';

        foreach ($targetEntities as $entityType => $entityIds)
        {
            switch ($entityType)
            {
                case PlatformGroupEntity::ENTITY_TYPE :
                    foreach ($entityIds as $group_id)
                    {
                        try
                        {
                            $group = $this->getGroupService()->findGroupByIdentifier($group_id);

                            $targetEntitiesHtml[] =
                                '<option>' . $translator->trans('GroupShort', [], Manager::CONTEXT) . ': ' .
                                $group->get_name() . '</option>';
                        }
                        catch (Exception)
                        {
                        }
                    }
                    break;
                case UserEntity::ENTITY_TYPE :
                    foreach ($entityIds as $user_id)
                    {
                        $targetEntitiesHtml[] =
                            '<option>' . $translator->trans('UserShort', [], Manager::CONTEXT) . ': ' .
                            $this->getUserService()->getUserFullNameByIdentifier($user_id) . '</option>';
                    }
                    break;
            }
        }

        $targetEntitiesHtml[] = '</select>';

        return implode(PHP_EOL, $targetEntitiesHtml);
    }
}
