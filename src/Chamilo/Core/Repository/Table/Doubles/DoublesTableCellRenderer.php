<?php
namespace Chamilo\Core\Repository\Table\Doubles;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class DoublesTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function render_cell($column, $content_object)
    {
        switch ($column->get_name())
        {
            case 'Duplicates' :
                $conditions = array();

                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_CONTENT_HASH),
                    new StaticConditionVariable($content_object->get_content_hash()));

                if ($this->condition)
                {
                    $conditions[] = $this->condition;
                }

                $condition = new AndCondition($conditions);

                return DataManager :: count_active_content_objects(
                    ContentObject :: class_name(),
                    new DataClassCountParameters($condition));
            case ContentObject :: PROPERTY_TITLE :
                $title = parent :: render_cell($column, $content_object);
                $title_short = StringUtilities :: getInstance()->truncate($title, 53, false);
                return '<a href="' .
                     htmlentities($this->get_component()->get_content_object_viewing_url($content_object)) . '" title="' .
                     $title . '">' . $title_short . '</a>';
            case Theme :: getInstance()->getCommonImage(
                'Action/Category',
                'png',
                Translation :: get('Type'),
                null,
                ToolbarItem :: DISPLAY_ICON) :
                return $content_object->get_icon_image(Theme :: ICON_MINI);

            case ContentObject :: PROPERTY_DESCRIPTION :
                return Utilities :: htmlentities(
                    StringUtilities :: getInstance()->truncate($content_object->get_description(), 50));
        }

        return parent :: render_cell($column, $content_object);
    }

    public function get_actions($content_object)
    {
        if ($this->get_table()->is_detail())
        {
            return null;
        }

        $toolbar = new Toolbar();
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('ViewItem'),
                Theme :: getInstance()->getCommonImagePath('Action/Browser'),
                $this->get_component()->get_url(array(Manager :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id())),
                ToolbarItem :: DISPLAY_ICON));
        return $toolbar->as_html();
    }
}
