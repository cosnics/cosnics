<?php
namespace Chamilo\Core\Repository\ContentObject\HotspotQuestion\Common\Import;

use Chamilo\Core\Repository\Common\Import\ContentObjectImport;
use Chamilo\Core\Repository\Common\Import\Cpo\CpoContentObjectImport;
use Chamilo\Core\Repository\ContentObject\HotspotQuestion\Common\ImportImplementation;

class CpoImportImplementation extends ImportImplementation
{

    public function import()
    {
        return ContentObjectImport :: launch($this);
    }

    public function post_import($content_object)
    {
        ContentObjectImport :: post_process($this, $content_object);
        
        $answers = $content_object->get_answers();
        
        foreach ($answers as &$answer)
        {
            $answer->set_answer(
                CpoContentObjectImport :: update_resources($this->get_controller(), $answer->get_answer()));
            $answer->set_comment(
                CpoContentObjectImport :: update_resources($this->get_controller(), $answer->get_comment()));
        }
        
        $content_object->set_answers($answers);
        
        $content_object->set_image(
            $this->get_controller()->get_content_object_id_cache_id($content_object->get_image()));
        
        $content_object->update();
    }
}
