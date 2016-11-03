<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Filter\Renderer;

use Chamilo\Core\Repository\ContentObject\Assignment\Filter\FilterData;
use Chamilo\Libraries\Platform\Translation;

/**
 * Render the parameters set via FilterData as HTML
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class HtmlFilterRenderer extends \Chamilo\Core\Repository\Filter\Renderer\HtmlFilterRenderer
{

    /*
     * (non-PHPdoc) @see \core\repository\filter\renderer\HtmlFilterRenderer::add_properties()
     */
    public function add_properties()
    {
        $filter_data = $this->get_filter_data();
        $html = array();

        $html[] = parent :: add_properties();

        // Start time
        if ($filter_data->has_date(FilterData :: FILTER_START_TIME))
        {
            $html[] = $this->renderParameter(
                $this->get_parameter_name(FilterData :: FILTER_START_TIME),
                Translation :: get(
                    'StartsBetween',
                    array(
                        'FROM' => $filter_data->get_start_time(FilterData :: FILTER_FROM_DATE),
                        'TO' => $filter_data->get_start_time(FilterData :: FILTER_TO_DATE))));
        }
        else
        {
            if ($filter_data->get_start_time(FilterData :: FILTER_FROM_DATE))
            {
                $html[] = $this->renderParameter(
                    $this->get_parameter_name(FilterData :: FILTER_START_TIME),
                    Translation :: get(
                        'StartsAfter',
                        array('FROM' => $filter_data->get_start_time(FilterData :: FILTER_FROM_DATE))));
            }
            elseif ($filter_data->get_start_time(FilterData :: FILTER_TO_DATE))
            {
                $html[] = $this->renderParameter(
                    $this->get_parameter_name(FilterData :: FILTER_START_TIME),
                    Translation :: get(
                        'StartsBefore',
                        array('TO' => $filter_data->get_start_time(FilterData :: FILTER_TO_DATE))));
            }
        }

        // End time
        if ($filter_data->has_date(FilterData :: FILTER_END_TIME))
        {
            $html[] = $this->renderParameter(
                $this->get_parameter_name(FilterData :: FILTER_END_TIME),
                Translation :: get(
                    'EndsBetween',
                    array(
                        'FROM' => $filter_data->get_end_time(FilterData :: FILTER_FROM_DATE),
                        'TO' => $filter_data->get_end_time(FilterData :: FILTER_TO_DATE))));
        }
        else
        {
            if ($filter_data->get_end_time(FilterData :: FILTER_FROM_DATE))
            {
                $html[] = $this->renderParameter(
                    $this->get_parameter_name(FilterData :: FILTER_END_TIME),
                    Translation :: get(
                        'EndsAfter',
                        array('FROM' => $filter_data->get_modification_time(FilterData :: FILTER_FROM_DATE))));
            }
            elseif ($filter_data->get_end_time(FilterData :: FILTER_TO_DATE))
            {
                $html[] = $this->renderParameter(
                    $this->get_parameter_name(FilterData :: FILTER_END_TIME),
                    Translation :: get(
                        'EndsBefore',
                        array('TO' => $filter_data->get_end_time(FilterData :: FILTER_TO_DATE))));
            }
        }

        return implode(PHP_EOL, $html);
    }
}