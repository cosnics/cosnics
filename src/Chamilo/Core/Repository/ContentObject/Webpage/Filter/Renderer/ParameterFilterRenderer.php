<?php
namespace Chamilo\Core\Repository\ContentObject\Webpage\Filter\Renderer;

use Chamilo\Core\Repository\ContentObject\Webpage\Filter\FilterData;

/**
 * Process the parameters set via FilterData based on the AJAX request to clear specific properties
 * 
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ParameterFilterRenderer extends \Chamilo\Core\Repository\Filter\Renderer\ParameterFilterRenderer
{

    public function render()
    {
        $filter_data = $this->get_filter_data();
        
        switch ($this->get_filter_property())
        {
            case FilterData :: FILTER_FILESIZE :
                $filter_data->set_filter_property(FilterData :: FILTER_COMPARE, null);
                $filter_data->set_filter_property(FilterData :: FILTER_FILESIZE, null);
                $filter_data->set_filter_property(FilterData :: FILTER_FORMAT, null);
                break;
        }
        
        parent :: render();
    }
}