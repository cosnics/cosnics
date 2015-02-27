<?php
namespace Chamilo\Core\Repository\ContentObject\Webpage\Filter\Renderer;

use Chamilo\Core\Repository\ContentObject\Webpage\Filter\FilterData;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;

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
        
        if ($filter_data->has_filter_property(FilterData :: FILTER_FILESIZE))
        {
            $format = $filter_data->get_filter_property(FilterData :: FILTER_FORMAT);
            $filesize = $filter_data->get_filter_property(FilterData :: FILTER_FILESIZE);
            $filesize_bytes = $filesize * pow(1024, $format);
            
            switch ($format)
            {
                case 1 :
                    $format = Translation :: get('KilobyteShort');
                    break;
                case 2 :
                    $format = Translation :: get('MegabyteShort');
                    break;
                case 3 :
                    $format = Translation :: get('GigabyteShort');
                    break;
            }
            
            $filesize_id = $this->get_parameter_name(FilterData :: FILTER_FILESIZE);
            $compare = $filter_data->get_filter_property(FilterData :: FILTER_COMPARE);
            
            if ($compare == ComparisonCondition :: EQUAL)
            {
                $html[] = '<div class="parameter" id="' . $filesize_id . '">' . Translation :: get(
                    'FilesizeAbout', 
                    array('SIZE' => $filesize, 'FORMAT' => $format)) . '</div>';
            }
            else
            {
                switch ($compare)
                {
                    case ComparisonCondition :: GREATER_THAN :
                        $html[] = '<div class="parameter" id="' . $filesize_id . '">' . Translation :: get(
                            'FilesizeGreaterThanShort', 
                            array('SIZE' => $filesize, 'FORMAT' => $format)) . '</div>';
                        break;
                    case ComparisonCondition :: LESS_THAN :
                        $html[] = '<div class="parameter" id="' . $filesize_id . '">' . Translation :: get(
                            'FilesizeLessThanShort', 
                            array('SIZE' => $filesize, 'FORMAT' => $format)) . '</div>';
                        break;
                }
            }
        }
        
        return implode(PHP_EOL, $html);
    }
}