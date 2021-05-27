<?php
namespace Chamilo\Core\Repository\Table\ExternalLink;

use Chamilo\Core\Repository\External\Manager as ExternalManager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData;
use Chamilo\Core\Repository\Manager as RepositoryManager;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class ExternalLinkTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function get_actions($object)
    {
        $toolbar = new Toolbar();
        if ($object->get_state() == SynchronizationData::STATE_ACTIVE)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('View', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('info-circle'),
                    $this->get_external_instance_viewing_url($object), ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('ExternalInstanceViewNotAllowed'),
                    new FontAwesomeGlyph('info-circle', array('text-muted')), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->as_html();
    }

    private function get_external_instance_viewing_url(SynchronizationData $external_instance_sync)
    {
        if (!$external_instance_sync || !$external_instance_sync->get_external())
        {
            return;
        }

        $parameters = ExternalManager::get_object_viewing_parameters($external_instance_sync);
        $parameters[RepositoryManager::PARAM_CONTEXT] = $external_instance_sync->get_external()->get_implementation();
        $parameters[RepositoryManager::PARAM_EXTERNAL_INSTANCE] = $external_instance_sync->get_external_id();

        return $this->get_component()->get_url($parameters);
    }

    public function render_cell($column, $object)
    {
        $external_instance = $object->get_external();

        if (!$external_instance)
        {
            return null;
        }

        switch ($column->get_name())
        {
            case Instance::PROPERTY_IMPLEMENTATION :
                $glyph = new NamespaceIdentGlyph(
                    $external_instance->get_implementation(), true, false, false, IdentGlyph::SIZE_MINI, [],
                    Translation::get('TypeName', null, $external_instance->get_implementation())
                );

                return $glyph->render();
            case Instance::PROPERTY_TITLE :
                return StringUtilities::getInstance()->truncate($external_instance->get_title(), 50);
        }

        return parent::render_cell($column, $object);
    }
}
