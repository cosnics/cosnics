<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Common\Export\Cpo;

use Chamilo\Core\Repository\Common\Export\ContentObjectExport;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Common\Export\CpoExportImplementation;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass\Configuration;

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
        $configurations = $this->get_content_object()->getConfiguration();
        
        // options
        foreach ($configurations as $configuration)
        {
            $configuration_node = $configurations_node->appendChild(
                $dom_document->createElement(CpoExportImplementation :: CONFIGURATION_NODE));
            
            $value = $configuration_node->appendChild($dom_document->createAttribute(Configuration :: PROPERTY_ID));
            $value->appendChild($dom_document->createTextNode($configuration->get_id()));
            
            foreach (Configuration :: get_default_property_names() as $property)
            {
                $value = $configuration_node->appendChild($dom_document->createAttribute($property));
                $value->appendChild($dom_document->createTextNode($configuration->get_default_property($property)));
            }
        }
    }
}