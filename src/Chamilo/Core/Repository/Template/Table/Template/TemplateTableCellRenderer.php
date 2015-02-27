<?php
namespace Chamilo\Core\Repository\Template\Table\Template;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Table\ContentObject\Table\RepositoryTableCellRenderer;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class TemplateTableCellRenderer extends RepositoryTableCellRenderer
{

    public function render_cell($column, $content_object)
    {
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TITLE :
                $title = parent :: render_cell($column, $content_object);
                $title_short = Utilities :: truncate_string($title, 53, false);
                return '<a href="' .
                     htmlentities($this->get_component()->get_content_object_viewing_url($content_object)) . '" title="' .
                     $title . '">' . $title_short . '</a>';
        }
        return parent :: render_cell($column, $content_object);
    }

    public function get_actions($content_object)
    {
        $toolbar = new Toolbar();
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('CopyToRepository'), 
                Theme :: getInstance()->getCommonImagesPath() . 'action_copy.png', 
                $this->get_component()->get_copy_content_object_url($content_object->get_id()), 
                ToolbarItem :: DISPLAY_ICON));
        
        if ($this->get_component()->get_user()->is_platform_admin())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('DeleteFromTemplates'), 
                    Theme :: getInstance()->getCommonImagesPath() . 'action_delete.png', 
                    $this->get_component()->get_delete_template_url($content_object->get_id()), 
                    ToolbarItem :: DISPLAY_ICON, 
                    true));
        }
        
        return $toolbar->as_html();
    }
}
