<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\TreeNode;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\ActionsTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Portfolio item table cell renderer
 *
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TreeNodeTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     * Renders a single cell
     *
     * @param TableColumn $column
     * @param TreeNode $node
     *
     * @return String
     */
    public function render_cell($column, $node)
    {
        if ($column instanceof ActionsTableColumn && $this instanceof TableCellRendererActionsColumnSupport)
        {
            return $this->get_actions($node);
        }

        $content_object = $node->getContentObject();

        switch ($column->get_name())
        {
            case ContentObject::PROPERTY_CREATION_DATE :
                return DatetimeUtilities::format_locale_date(
                    Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES),
                    $content_object->get_creation_date()
                );
            case ContentObject::PROPERTY_MODIFICATION_DATE :
                return DatetimeUtilities::format_locale_date(
                    Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES),
                    $content_object->get_modification_date()
                );
        }

        return $node->getContentObject()->get_default_property($column->get_name());
    }

    /**
     * Returns the actions toolbar
     *
     * @param TreeNode $node
     *
     * @return string
     */
    public function get_actions($node)
    {
        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);

        if ($this->get_component()->get_parent()->is_allowed_to_view_content_object($node))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('ViewerComponent'),
                    Theme::getInstance()->getImagePath(
                        'Chamilo\Core\Repository\ContentObject\LearningPath\Display',
                        'Action/' . Manager::ACTION_VIEW_COMPLEX_CONTENT_OBJECT
                    ),
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_VIEW_COMPLEX_CONTENT_OBJECT,
                            Manager::PARAM_CHILD_ID => $node->getId()
                        )
                    ),
                    ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('ViewNotAllowed'),
                    Theme::getInstance()->getImagePath(
                        'Chamilo\Core\Repository\ContentObject\LearningPath\Display',
                        'Action/' . Manager::ACTION_VIEW_COMPLEX_CONTENT_OBJECT . 'Na'
                    ),
                    null,
                    ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($this->get_component()->canEditTreeNode($node->getParentNode()))
        {
            $variable = $node->getContentObject() instanceof LearningPath ? 'MoveFolder' : 'MoverComponent';

            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get($variable),
                    Theme::getInstance()->getImagePath(
                        'Chamilo\Core\Repository\ContentObject\LearningPath\Display',
                        'Action/' . Manager::ACTION_MOVE
                    ),
                    $this->get_component()->get_url(
                        array(Manager::PARAM_ACTION => Manager::ACTION_MOVE, Manager::PARAM_CHILD_ID => $node->getId())
                    ),
                    ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($this->get_component()->canEditTreeNode($node->getParentNode()))
        {
            $variable = $node->getContentObject() instanceof LearningPath ? 'DeleteFolder' : 'DeleterComponent';

            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get($variable),
                    Theme::getInstance()->getImagePath(
                        'Chamilo\Core\Repository\ContentObject\LearningPath\Display',
                        'Action/' . Manager::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM
                    ),
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM,
                            Manager::PARAM_CHILD_ID => $node->getId()
                        )
                    ),
                    ToolbarItem::DISPLAY_ICON,
                    true
                )
            );
        }

        return $toolbar->as_html();
    }

    /**
     * @param TreeNode $data_class
     *
     * @return int
     */
    public function render_id_cell($data_class)
    {
        return $data_class->getId();
    }
}