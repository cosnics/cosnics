<?php
namespace Chamilo\Core\Repository\ContentObject\File\Filter\Renderer;

use Chamilo\Core\Repository\ContentObject\File\Filter\FilterData;

/**
 * Process the parameters set via FilterData based on the AJAX request to clear specific properties
 * 
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ParameterFilterRenderer extends \Chamilo\Core\Repository\Filter\Renderer\ParameterFilterRenderer
{
    
    /*
     * (non-PHPdoc) @see \core\repository\filter\renderer\ParameterFilterRenderer::render()
     */
    public function render()
    {
        $filter_data = $this->get_filter_data();
        
        switch ($this->get_filter_property())
        {
            case FilterData :: FILTER_EXTENSION :
                $filter_data->set_filter_property(FilterData :: FILTER_EXTENSION, null);
                $filter_data->set_filter_property(FilterData :: FILTER_EXTENSION_TYPE, null);
                break;
            case FilterData :: FILTER_EXTENSION_TYPE :
                $filter_data->set_filter_property(FilterData :: FILTER_EXTENSION, null);
                $filter_data->set_filter_property(FilterData :: FILTER_EXTENSION_TYPE, null);
                break;
            case FilterData :: FILTER_FILESIZE :
                $filter_data->set_filter_property(FilterData :: FILTER_COMPARE, null);
                $filter_data->set_filter_property(FilterData :: FILTER_FILESIZE, null);
                $filter_data->set_filter_property(FilterData :: FILTER_FORMAT, null);
                break;
        }
        
        parent :: render();
    }
}