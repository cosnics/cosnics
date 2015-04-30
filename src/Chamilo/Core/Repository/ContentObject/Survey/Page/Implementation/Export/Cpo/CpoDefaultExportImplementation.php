<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Implementation\Export\Cpo;

use Chamilo\Core\Repository\Common\Export\ContentObjectExport;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Implementation\Export\CpoExportImplementation;

/**
 *
 * @package repository.content_object.survey_matrix_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class CpoDefaultExportImplementation extends CpoExportImplementation
{

    function render()
    {
        ContentObjectExport :: launch($this);
        
        $content_object_node = $this->get_context()->get_content_object_sub_node(
            CpoExportImplementation :: PAGE_CONFIGURATIONS, 
            $this->get_content_object()->get_id());
        
        $dom_document = $this->get_context()->get_dom_document();
        
        $configurations_node = $content_object_node->appendChild(
            $dom_document->createElement(CpoExportImplementation :: CONFIGURATIONS_NODE));
        $configurations = $this->get_content_object()->getConfigurations();
                
        // options
        while ($configuration = $configurations->next_result())
        {
      
            $configuration_node = $configurations_node->appendChild(
                $dom_document->createElement(CpoExportImplementation :: CONFIGURATION_NODE));
            
            $value = $configuration_node->appendChild($dom_document->createAttribute('id'));
            $value->appendChild($dom_document->createTextNode($configuration->get_id()));
            
            $display_order = $configuration_node->appendChild($dom_document->createAttribute('display_order'));
            $display_order->appendChild($dom_document->createTextNode($configuration->get_display_order()));
            
            $value = $configuration_node->appendChild($dom_document->createAttribute('value'));
            $value->appendChild($dom_document->createTextNode($configuration->get_value()));
        }
     
       
    }
}