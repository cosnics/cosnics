<?php
namespace Chamilo\Core\Repository\Quota\Rights\Table;

use Chamilo\Core\Repository\Manager as RepositoryManager;
use Chamilo\Core\Repository\Quota\Manager as QuotaManager;
use Chamilo\Core\Repository\Quota\Rights\Manager;
use Chamilo\Core\Repository\Quota\Rights\Table\Entity\EntityTableColumnModel;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\SortableStaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight as RightsLocationEntityRightAlias;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Quota\Rights\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EntityTableRenderer extends RecordListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const PROPERTY_ENTITY_DESCRIPTION = 'entity_description';
    public const PROPERTY_ENTITY_GLYPH = 'entity_glyph';
    public const PROPERTY_ENTITY_TITLE = 'entity_title';
    public const PROPERTY_GROUP_NAME = 'group_name';
    public const PROPERTY_GROUP_PATH = 'group_path';

    public const TABLE_IDENTIFIER = Manager::PARAM_LOCATION_ENTITY_RIGHT_GROUP_ID;

    protected User $user;

    public function __construct(
        User $user, Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer,
        Pager $pager
    )
    {
        $this->user = $user;

        parent::__construct($translator, $urlGenerator, $htmlTableRenderer, $pager);
    }

    public function getTableActions(): TableActions
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $deleteUrl = $urlGenerator->fromParameters(
            [
                Application::PARAM_CONTEXT => RepositoryManager::CONTEXT,
                Application::PARAM_ACTION => RepositoryManager::ACTION_QUOTA,
                QuotaManager::PARAM_ACTION => QuotaManager::ACTION_RIGHTS,
                Manager::PARAM_ACTION => Manager::ACTION_DELETE
            ]
        );

        $actions->addAction(
            new TableAction(
                $deleteUrl, $translator->trans('RemoveSelected', [], StringUtilities::LIBRARIES)
            )
        );

        return $actions;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    protected function initializeColumns()
    {
        $translator = $this->getTranslator();

        $this->addColumn(
            new SortableStaticTableColumn(
                RightsLocationEntityRightAlias::PROPERTY_ENTITY_TYPE,
                $translator->trans('EntityType', [], 'Chamilo\Core\Repository\Quota\Rights')
            )
        );

        $this->addColumn(
            new SortableStaticTableColumn(
                self::PROPERTY_ENTITY_TITLE,
                $translator->trans('EntityTitle', [], 'Chamilo\Core\Repository\Quota\Rights')
            )
        );

        $this->addColumn(
            new SortableStaticTableColumn(
                self::PROPERTY_ENTITY_DESCRIPTION,
                $translator->trans('EntityDescription', [], 'Chamilo\Core\Repository\Quota\Rights')
            )
        );

        $this->addColumn(
            new SortableStaticTableColumn(
                self::PROPERTY_GROUP_NAME, $translator->trans('Group', [], 'Chamilo\Core\Repository\Quota\Rights')
            )
        );

        $this->addColumn(
            new SortableStaticTableColumn(
                self::PROPERTY_GROUP_PATH, $translator->trans('Path', [], 'Chamilo\Core\Repository\Quota\Rights')
            )
        );
    }

    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $record): string
    {
        if ($column->get_name() == RightsLocationEntityRightAlias::PROPERTY_ENTITY_TYPE)
        {
            /**
             * @var \Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph $glyph ;
             */
            $glyph = $record[EntityTableColumnModel::PROPERTY_ENTITY_GLYPH];

            return $glyph->render();
        }

        return parent::renderCell($column, $resultPosition, $record);
    }

    public function renderTableRowActions(TableResultPosition $resultPosition, $record): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        if ($this->getUser()->is_platform_admin())
        {
            $deleteUrl = $urlGenerator->fromParameters(
                [
                    Application::PARAM_CONTEXT => RepositoryManager::CONTEXT,
                    Application::PARAM_ACTION => RepositoryManager::ACTION_QUOTA,
                    QuotaManager::PARAM_ACTION => QuotaManager::ACTION_RIGHTS,
                    Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                    Manager::PARAM_LOCATION_ENTITY_RIGHT_GROUP_ID => $record[DataClass::PROPERTY_ID]
                ]
            );

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                    $deleteUrl, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->render();
    }
}
