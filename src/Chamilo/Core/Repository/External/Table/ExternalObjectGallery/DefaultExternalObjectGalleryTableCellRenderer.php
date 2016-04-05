<?php
namespace Chamilo\Core\Repository\External\Table\ExternalObjectGallery;

use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Table\Extension\GalleryTable\Extension\DataClassGalleryTable\DataClassGalleryTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;

abstract class DefaultExternalObjectGalleryTableCellRenderer extends DataClassGalleryTableCellRenderer implements
    TableCellRendererActionsColumnSupport

{

    public function get_actions($object)
    {
        $toolbar = new Toolbar();
        $toolbar->add_items($this->get_component()->get_external_repository_object_actions($object));
        return $toolbar->as_html();
    }
}