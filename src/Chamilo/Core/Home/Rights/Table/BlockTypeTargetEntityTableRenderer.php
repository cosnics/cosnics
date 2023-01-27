<?php
namespace Chamilo\Core\Home\Rights\Table;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Home\Rights\Manager;
use Chamilo\Core\Home\Rights\Storage\DataClass\BlockTypeTargetEntity;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordListTableRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Home\Rights\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BlockTypeTargetEntityTableRenderer extends RecordListTableRenderer implements TableRowActionsSupport
{

    protected function initializeColumns()
    {
        $translator = $this->getTranslator();

        $this->addColumn(
            new DataClassPropertyTableColumn(
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
    public function renderCell(TableColumn $column, $learningPathChildAttempt): string
    {
        if ($column->get_name() == 'target_entities')
        {
            return $this->renderTargetEntities((array) $learningPathChildAttempt['target_entities']);
        }

        return parent::renderCell($column, $learningPathChildAttempt);
    }

    public function renderTableRowActions($result): string
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
                        $group = \Chamilo\Core\Group\Storage\DataManager::retrieve_by_id(Group::class, $group_id);
                        if ($group)
                        {
                            $targetEntitiesHtml[] =
                                '<option>' . $translator->trans('GroupShort', [], Manager::CONTEXT) . ': ' .
                                $group->get_name() . '</option>';
                        }
                    }
                    break;
                case UserEntity::ENTITY_TYPE :
                    foreach ($entityIds as $user_id)
                    {
                        $targetEntitiesHtml[] =
                            '<option>' . $translator->trans('UserShort', [], Manager::CONTEXT) . ': ' .
                            DataManager::get_fullname_from_user($user_id) . '</option>';
                    }
                    break;
            }
        }

        $targetEntitiesHtml[] = '</select>';

        return implode(PHP_EOL, $targetEntitiesHtml);
    }
}
