<?php
namespace Chamilo\Core\Repository\Instance\Table;

use Chamilo\Core\Repository\Instance\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Repository\Instance\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class InstanceTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport
{
    public const TABLE_IDENTIFIER = Manager::PARAM_INSTANCE_ID;

    protected function initializeColumns()
    {
        $this->addColumn(
            new DataClassPropertyTableColumn(Instance::class, Instance::PROPERTY_IMPLEMENTATION)
        );
        $this->addColumn(new DataClassPropertyTableColumn(Instance::class, Instance::PROPERTY_TITLE));
    }

    /**
     * @param \Chamilo\Core\Repository\Instance\Storage\DataClass\Instance $instance
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $instance): string
    {
        $translator = $this->getTranslator();

        if ($column->get_name() == Instance::PROPERTY_IMPLEMENTATION)
        {
            $name = htmlentities($translator->trans('ImplementationName', [], $instance->get_implementation()));

            $glyph = new NamespaceIdentGlyph(
                $instance->get_implementation(), true, false, false, IdentGlyph::SIZE_SMALL, [], $name
            );

            return $glyph->render();
        }

        return parent::renderCell($column, $resultPosition, $instance);
    }

    /**
     * @param \Chamilo\Core\Repository\Instance\Storage\DataClass\Instance $instance
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $instance): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        if ($instance->is_enabled())
        {
            $deactivateUrl = $urlGenerator->fromParameters([
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_DEACTIVATE,
                Manager::PARAM_INSTANCE_ID => $instance->getId()
            ]);

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Deactivate', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('pause-cicle', [], null, 'fas'), $deactivateUrl, ToolbarItem::DISPLAY_ICON,
                    true
                )
            );
        }
        else
        {
            $activateUrl = $urlGenerator->fromParameters([
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_ACTIVATE,
                Manager::PARAM_INSTANCE_ID => $instance->getId()
            ]);

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Activate', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('play-cicle', [], null, 'fas'), $activateUrl, ToolbarItem::DISPLAY_ICON, true
                )
            );
        }

        $updateUrl = $urlGenerator->fromParameters([
            Application::PARAM_CONTEXT => Manager::CONTEXT,
            Application::PARAM_ACTION => Manager::ACTION_UPDATE,
            Manager::PARAM_INSTANCE_ID => $instance->getId()
        ]);

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                $updateUrl, ToolbarItem::DISPLAY_ICON
            )
        );

        $deleteUrl = $urlGenerator->fromParameters([
            Application::PARAM_CONTEXT => Manager::CONTEXT,
            Application::PARAM_ACTION => Manager::ACTION_DELETE,
            Manager::PARAM_INSTANCE_ID => $instance->getId()
        ]);

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'), $deleteUrl,
                ToolbarItem::DISPLAY_ICON, true
            )
        );

        $rightsUrl = $urlGenerator->fromParameters([
            Application::PARAM_CONTEXT => Manager::CONTEXT,
            Application::PARAM_ACTION => Manager::ACTION_RIGHTS,
            Manager::PARAM_INSTANCE_ID => $instance->getId()
        ]);

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('ManageRights', [], \Chamilo\Core\Rights\Manager::CONTEXT),
                new FontAwesomeGlyph('lock'), $rightsUrl, ToolbarItem::DISPLAY_ICON
            )
        );

        return $toolbar->render();
    }
}
