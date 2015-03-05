<?php
namespace Chamilo\Core\Metadata\Attribute\Table\Attribute;

use Chamilo\Core\Metadata\Attribute\Manager;
use Chamilo\Core\Metadata\Attribute\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Table cell renderer for the schema
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AttributeTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     * Renders a single cell
     *
     * @param TableColumn $column
     * @param DataClass $result
     *
     * @return string
     */
    public function render_cell($column, $result)
    {
        switch ($column->get_name())
        {
            case AttributeTableColumnModel :: COLUMN_PREFIX :
                return $result->get_namespace();
                break;
            case AttributeTableColumnModel :: COLUMN_CONTROLLED_VOCABULARY :
                $has_controlled_vocabulary = DataManager :: attribute_has_controlled_vocabulary($result->get_id()) ? 'true' : 'false';

                return Theme :: getInstance()->getCommonImage('action_setting_' . $has_controlled_vocabulary);
                break;
        }

        return parent :: render_cell($column, $result);
    }

    /**
     * Returns the actions toolbar
     *
     * @param mixed $result
     *
     * @return String
     */
    public function get_actions($result)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        if ($result->is_fixed())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('EditNA', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/EditNa'),
                    null,
                    ToolbarItem :: DISPLAY_ICON));

            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('DeleteNA', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/DeleteNa'),
                    null,
                    ToolbarItem :: DISPLAY_ICON));

            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('ManageControlledVocabularyNA', null, 'core\metadata'),
                    Theme :: getInstance()->getImagePath(
                        'Chamilo\Core\Metadata\Attribute',
                        'Action/ControlledVocabularyNa'),
                    null,
                    ToolbarItem :: DISPLAY_ICON));

            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('MetadataDefaultValuesNA', null, 'core\metadata'),
                    Theme :: getInstance()->getImagePath('Chamilo\Core\Metadata\Attribute', 'Action/DefaultNa'),
                    null,
                    ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Edit'),
                    $this->get_component()->get_url(
                        array(
                            Manager :: PARAM_ACTION => Manager :: ACTION_UPDATE,
                            Manager :: PARAM_ATTRIBUTE_ID => $result->get_id())),
                    ToolbarItem :: DISPLAY_ICON));

            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Delete'),
                    $this->get_component()->get_url(
                        array(
                            Manager :: PARAM_ACTION => Manager :: ACTION_DELETE,
                            Manager :: PARAM_ATTRIBUTE_ID => $result->get_id())),
                    ToolbarItem :: DISPLAY_ICON,
                    true));

            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('ManageControlledVocabulary', null, 'core\metadata'),
                    Theme :: getInstance()->getImagePath(
                        'Chamilo\Core\Metadata\Attribute',
                        'Action/ControlledVocabulary'),
                    $this->get_component()->get_url(
                        array(
                            Manager :: PARAM_ACTION => Manager :: ACTION_VOCABULATE,
                            Manager :: PARAM_ATTRIBUTE_ID => $result->get_id())),
                    ToolbarItem :: DISPLAY_ICON));

            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('MetadataDefaultValues', null, 'core\metadata'),
                    Theme :: getInstance()->getImagePath('Chamilo\Core\Metadata\Attribute', 'Action/Default'),
                    $this->get_component()->get_url(
                        array(
                            Manager :: PARAM_ACTION => null,
                            \Chamilo\Core\Metadata\Manager :: PARAM_ACTION => \Chamilo\Core\Metadata\Manager :: ACTION_VALUE,
                            \Chamilo\Core\Metadata\Value\Manager :: PARAM_ACTION => \Chamilo\Core\Metadata\Value\Manager :: ACTION_ATTRIBUTE,
                            Manager :: PARAM_ATTRIBUTE_ID => $result->get_id())),
                    ToolbarItem :: DISPLAY_ICON));
        }

        return $toolbar->as_html();
    }
}