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
 * @package Chamilo\Core\Metadata\Vocabulary\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class VocabularyTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const COLUMN_DEFAULT = 'default';

    public const TABLE_IDENTIFIER = Manager::PARAM_VOCABULARY_ID;

    protected StringUtilities $stringUtilities;

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    public function getTableActions(): TableActions
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();
        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $actions->addAction(
            new TableAction(
                $urlGenerator->fromParameters(
                    [Application::PARAM_CONTEXT => Manager::CONTEXT, Manager::PARAM_ACTION => Manager::ACTION_DELETE]
                ), $translator->trans('RemoveSelected', [], StringUtilities::LIBRARIES)
            )
        );

        return $actions;
    }

    protected function initializeColumns(): void
    {
        $translator = $this->getTranslator();

        $this->addColumn(
            new DataClassPropertyTableColumn(
                Vocabulary::class, Vocabulary::PROPERTY_VALUE, $translator->trans(
                $this->getStringUtilities()->createString(Vocabulary::PROPERTY_VALUE)->upperCamelize()->toString(), [],
                'Chamilo\Core\Metadata'
            )
            )
        );

        $glyph = new FontAwesomeGlyph(
            'check-circle', [], $translator->trans('Default', [], Manager::CONTEXT), 'fas'
        );

        $this->addColumn(
            new StaticTableColumn(
                self::COLUMN_DEFAULT, $glyph->render()
            )
        );
    }

    /**
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Vocabulary $vocabulary
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $vocabulary): string
    {
        if ($column->get_name() == self::COLUMN_DEFAULT)
        {
            $urlGenerator = $this->getUrlGenerator();
            $translator = $this->getTranslator();
            $translationVariable = $vocabulary->isDefault() ? 'Default' : 'DefaultNa';

            $extraClasses = $vocabulary->isDefault() ? [] : ['text-muted'];

            $glyph = new FontAwesomeGlyph(
                'check-circle', $extraClasses, $translator->trans($translationVariable, [], Manager::CONTEXT), 'fas'
            );

            $link = $urlGenerator->fromParameters(
                [
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Manager::PARAM_ACTION => Manager::ACTION_DEFAULT,
                    Manager::PARAM_VOCABULARY_ID => $vocabulary->getId()
                ]
            );

            if ($vocabulary->isDefault())
            {
                return $glyph->render();
            }
            else
            {
                return '<a href="' . $link . '">' . $glyph->render() . '</a>';
            }
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
                $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                $urlGenerator->fromParameters(
                    [
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Manager::PARAM_ACTION => Manager::ACTION_UPDATE,
                        Manager::PARAM_VOCABULARY_ID => $vocabulary->getId()
                    ]
                ), ToolbarItem::DISPLAY_ICON
            )
        );

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                $urlGenerator->fromParameters(
                    [
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                        Manager::PARAM_VOCABULARY_ID => $vocabulary->getId()
                    ]
                ), ToolbarItem::DISPLAY_ICON, true
            )
        );

        return $toolbar->render();
    }
}
