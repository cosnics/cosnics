<?php
namespace Chamilo\Core\Repository\Table\ImpactView;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Description of impact_view_cell_renderer
 *
 * @author Pieterjan Broekaert
 */
class ImpactViewTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function render_cell($column, $content_object)
    {
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TYPE :
                return $content_object->get_icon_image(Theme :: ICON_MINI);
            case ImpactViewTableColumnModel :: COLUMN_CATEGORY :
                if ($content_object->get_parent_id() != 0)
                {
                    $category = DataManager :: retrieve_by_id(
                        RepositoryCategory :: class_name(),
                        $content_object->get_parent_id());

                    return $category->get_name();
                }
                else
                {
                    return Translation :: get('MyRepository');
                }

            case ImpactViewTableColumnModel :: COLUMN_SAFE_DELETE :
                return $this->render_is_linked_column($content_object);
        }

        return parent :: render_cell($column, $content_object);
    }

    public function get_actions($object)
    {
        $toolbar = new Toolbar();
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('Preview', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('Action/Preview'),
                $this->get_content_object_preview_url($object),
                ToolbarItem :: DISPLAY_ICON,
                false,
                null,
                null,
                null,
                array(
                    'onclick' => 'javascript:openPopup(\'' . $this->get_content_object_preview_url($object) .
                         '\');return false;')));

        return $toolbar->as_html();
    }

    private function render_is_linked_column($content_object)
    {
        if (! DataManager :: content_object_deletion_allowed($content_object))
        {
            return '<img src="' . Theme :: getInstance()->getCommonImagePath('Status/WarningMini') . '" /> ' .
                 Translation :: getInstance()->getTranslation('PublicationsFound', array(), Manager :: context());
        }
        else
        {
            return '<img src="' . Theme :: getInstance()->getCommonImagePath('Status/OkMini') . '" />';
        }
    }

    /**
     * Returns the url to the content object preview
     *
     * @param ContentObject $content_object
     *
     * @return string
     */
    public function get_content_object_preview_url($content_object)
    {
        return $this->get_component()->get_url(
            array(
                Manager :: PARAM_ACTION => Manager :: ACTION_VIEW_CONTENT_OBJECTS,
                Manager :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id(),
                Manager :: PARAM_CATEGORY_ID => $content_object->get_parent_id()));
    }
}
