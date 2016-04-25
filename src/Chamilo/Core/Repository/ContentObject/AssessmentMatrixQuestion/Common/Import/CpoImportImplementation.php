<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatrixQuestion\Common\Import;

use Chamilo\Core\Repository\Common\Import\ContentObjectImport;
use Chamilo\Core\Repository\Common\Import\Cpo\CpoContentObjectImport;
use Chamilo\Core\Repository\ContentObject\AssessmentMatrixQuestion\Common\ImportImplementation;

class CpoImportImplementation extends ImportImplementation
{

    public function import()
    {
        return ContentObjectImport :: launch($this);
    }

    public function post_import($content_object)
    {
        ContentObjectImport :: post_process($this, $content_object);
        
        $options = $content_object->get_options();
        
        foreach ($options as &$option)
        {
            $option->set_value(
                CpoContentObjectImport :: update_resources($this->get_controller(), $option->get_value()));
            $option->set_feedback(
                CpoContentObjectImport :: update_resources($this->get_controller(), $option->get_feedback()));
        }
        
        $content_object->set_options($options);
        
        $matches = $content_object->get_matches();
        
        foreach ($matches as &$match)
        {
            $match = CpoContentObjectImport :: update_resources($this->get_controller(), $match);
        }
        
        $content_object->set_matches($matches);
        
        $content_object->update();
    }
}
