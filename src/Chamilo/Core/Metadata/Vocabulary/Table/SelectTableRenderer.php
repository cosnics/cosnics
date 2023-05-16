<?php
namespace Chamilo\Core\Metadata\Vocabulary\Table;

use Chamilo\Core\Metadata\Storage\DataClass\Vocabulary;
use Chamilo\Core\Metadata\Vocabulary\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Metadata\Relation\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SelectTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const COLUMN_TYPE = 'type';

    public const TABLE_IDENTIFIER = Manager::PARAM_VOCABULARY_ID;

    public function getTableActions(): TableActions
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();
        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $actions->addAction(
            new TableAction(
                $urlGenerator->fromRequest(), $translator->trans('RemoveSelected', [], StringUtilities::LIBRARIES)
            )
        );

        return $actions;
    }

    protected function initializeColumns()
    {
        $translator = $this->getTranslator();
        $glyph = new FontAwesomeGlyph(
            'folder', [], $translator->trans('Type', [], Manager::CONTEXT), 'fas'
        );

        $this->addColumn(
            new StaticTableColumn(self::COLUMN_TYPE, $glyph->render())
        );

        $this->addColumn($this->getDataClassPropertyTableColumnFactory()->getColumn(Vocabulary::class, Vocabulary::PROPERTY_VALUE));
    }

    /**
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Vocabulary $vocabulary
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $vocabulary): string
    {
        if ($column->get_name() == self::COLUMN_TYPE)
        {
            if ($vocabulary->get_user_id() == 0)
            {
                $image = 'globe';
                $translationVariable = 'Predefined';
            }
            else
            {
                $image = 'users';
                $translationVariable = 'UserDefined';
            }

            $glyph = new FontAwesomeGlyph(
                $image, [], $this->getTranslator()->trans($translationVariable, [], Manager::CONTEXT), 'fas'
            );

            return $glyph->render();
        }

        return parent::renderCell($column, $resultPosition, $vocabulary);
    }

    /**
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Vocabulary $vocabulary
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $vocabulary): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Add', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('plus'),
                $urlGenerator->fromRequest(
                    [
                        Application::PARAM_CONTEXT => \Chamilo\Core\Metadata\Vocabulary\Ajax\Manager::CONTEXT,
                        Application::PARAM_ACTION => \Chamilo\Core\Metadata\Vocabulary\Ajax\Manager::ACTION_SELECT,
                        Manager::PARAM_VOCABULARY_ID => $vocabulary->getId()
                    ]
                ), ToolbarItem::DISPLAY_ICON
            )
        );

        return $toolbar->render();
    }
}
