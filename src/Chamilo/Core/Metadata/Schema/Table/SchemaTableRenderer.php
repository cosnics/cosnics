<?php
namespace Chamilo\Core\Metadata\Schema\Table;

use Chamilo\Core\Metadata\Schema\Manager;
use Chamilo\Core\Metadata\Storage\DataClass\Schema;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Metadata\Relation\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SchemaTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const TABLE_IDENTIFIER = Manager::PARAM_SCHEMA_ID;

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

    protected function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(Schema::class, Schema::PROPERTY_NAMESPACE));
        $this->addColumn(new DataClassPropertyTableColumn(Schema::class, Schema::PROPERTY_NAME));
        $this->addColumn(new DataClassPropertyTableColumn(Schema::class, Schema::PROPERTY_URL));
    }

    /**
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Schema $schema
     */
    public function renderTableRowActions($schema): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Elements', [], Manager::CONTEXT), new FontAwesomeGlyph('file'),
                $urlGenerator->fromParameters(
                    [
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Manager::PARAM_ACTION => Manager::ACTION_ELEMENT,
                        Manager::PARAM_SCHEMA_ID => $schema->getId()
                    ]
                ), ToolbarItem::DISPLAY_ICON
            )
        );

        if ($schema->is_fixed())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('EditNA', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('pencil-alt', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                )
            );

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('DeleteNA', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('times', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                    $urlGenerator->fromParameters(
                        [
                            Application::PARAM_CONTEXT => Manager::CONTEXT,
                            Manager::PARAM_ACTION => Manager::ACTION_UPDATE,
                            Manager::PARAM_SCHEMA_ID => $schema->getId()
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
                            Manager::PARAM_SCHEMA_ID => $schema->getId()
                        ]
                    ), ToolbarItem::DISPLAY_ICON, true
                )
            );
        }

        return $toolbar->render();
    }
}
