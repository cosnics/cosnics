<?php
namespace Chamilo\Core\Repository\Common\Export\Cpo;

use Chamilo\Core\Repository\Common\Export\ContentObjectExport;

class CpoContentObjectExport extends ContentObjectExport
{

    public function external_sync()
    {
        $document = $this->get_export_implementation()->get_context()->get_dom_document();
        $content_object = $this->get_export_implementation()->get_content_object();
        
        $content_object_external_sync = $content_object->get_synchronization_data();
        
        if ($content_object_external_sync)
        {
            $content_object_node = $this->get_export_implementation()->get_context()->get_content_object_node(
                $content_object->get_id());
            
            $external_sync = $document->createElement('external_sync');
            $content_object_node->appendChild($external_sync);
            
            $id = $external_sync->appendChild($document->createAttribute('id'));
            $id->appendChild($document->createTextNode($content_object_external_sync->get_external_object_id()));
            
            $timestamp = $external_sync->appendChild($document->createAttribute('timestamp'));
            $timestamp->appendChild(
                $document->createTextNode($content_object_external_sync->get_external_object_timestamp()));
            
            $external_instance = $external_sync->appendChild($document->createAttribute('external_instance'));
            $external_instance->appendChild($document->createTextNode($content_object_external_sync->get_external_id()));
            
            $this->get_export_implementation()->get_context()->process_external_instance(
                $content_object_external_sync->get_external_id());
        }
    }
}
