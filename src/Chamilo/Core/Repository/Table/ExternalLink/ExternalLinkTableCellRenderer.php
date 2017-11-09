<?php
namespace Chamilo\Core\Repository\Table\ExternalLink;

use Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class ExternalLinkTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function render_cell($column, $object)
    {
        $external_instance = $object->get_external();
        
        if (! $external_instance)
        {
            return null;
        }
        
        switch ($column->get_name())
        {
            case \Chamilo\Core\Repository\Instance\Storage\DataClass\Instance::PROPERTY_IMPLEMENTATION :
                return Theme::getInstance()->getImage(
                    'Logo/' . Theme::ICON_MINI, 
                    'png', 
                    Translation::get('TypeName', null, $external_instance->get_implementation()), 
                    null, 
                    ToolbarItem::DISPLAY_ICON, 
                    false, 
                    $external_instance->get_implementation());
            case \Chamilo\Core\Repository\Instance\Storage\DataClass\Instance::PROPERTY_TITLE :
                return StringUtilities::getInstance()->truncate($external_instance->get_title(), 50);
        }
        
        return parent::render_cell($column, $object);
    }

    public function get_actions($object)
    {
        $toolbar = new Toolbar();
        if ($object->get_state() == SynchronizationData::STATE_ACTIVE)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('View', null, Utilities::COMMON_LIBRARIES), 
                    Theme::getInstance()->getCommonImagePath('Action/Details'), 
                    $this->get_external_instance_viewing_url($object), 
                    ToolbarItem::DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('ExternalInstanceViewNotAllowed'), 
                    Theme::getInstance()->getCommonImagePath('Action/DetailsNa'), 
                    null, 
                    ToolbarItem::DISPLAY_ICON));
        }
        return $toolbar->as_html();
    }

    private function get_external_instance_viewing_url(SynchronizationData $external_instance_sync)
    {
        if (! $external_instance_sync || ! $external_instance_sync->get_external())
        {
            return;
        }
        
        $parameters = \Chamilo\Core\Repository\External\Manager::get_object_viewing_parameters($external_instance_sync);
        $parameters[\Chamilo\Core\Repository\Manager::PARAM_CONTEXT] = $external_instance_sync->get_external()->get_implementation();
        $parameters[\Chamilo\Core\Repository\Manager::PARAM_EXTERNAL_INSTANCE] = $external_instance_sync->get_external_id();
        
        return $this->get_component()->get_url($parameters);
    }
}
