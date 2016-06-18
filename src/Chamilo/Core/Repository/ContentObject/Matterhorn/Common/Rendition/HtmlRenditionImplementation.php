<?php
namespace Chamilo\Core\Repository\ContentObject\Matterhorn\Common\Rendition;

use Chamilo\Core\Repository\ContentObject\Matterhorn\Common\RenditionImplementation;
use Chamilo\Libraries\Format\Table\PropertiesTable;

class HtmlRenditionImplementation extends RenditionImplementation
{

    public function get_object()
    {
        return $this->get_content_object()->get_synchronization_data()->get_external_object();
    }

    public function get_properties_table()
    {
        $properties = $this->get_display_properties();
        
        $table = new PropertiesTable($properties);
        $table->setAttribute('style', 'margin-top: 1em; margin-bottom: 0;');
        return $table->toHtml();
    }
}
