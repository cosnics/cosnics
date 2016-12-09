<?php
namespace Chamilo\Core\Repository\ContentObject\File\Filter\Renderer;

use Chamilo\Core\Repository\ContentObject\File\Filter\FilterData;
use Chamilo\Libraries\File\FileType;
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
        
        $html[] = parent::add_properties();
        
        if ($filter_data->has_filter_property(FilterData::FILTER_FILESIZE))
        {
            $format = $filter_data->get_filter_property(FilterData::FILTER_FORMAT);
            $filesize = $filter_data->get_filter_property(FilterData::FILTER_FILESIZE);
            $filesize_bytes = $filesize * pow(1024, $format);
            
            switch ($format)
            {
                case 1 :
                    $format = Translation::get('KilobyteShort');
                    break;
                case 2 :
                    $format = Translation::get('MegabyteShort');
                    break;
                case 3 :
                    $format = Translation::get('GigabyteShort');
                    break;
            }
            
            $filesize_id = $this->get_parameter_name(FilterData::FILTER_FILESIZE);
            $compare = $filter_data->get_filter_property(FilterData::FILTER_COMPARE);
            
            if ($compare == ComparisonCondition::EQUAL)
            {
                $html[] = $this->renderParameter(
                    $filesize_id, 
                    Translation::get('FilesizeAbout', array('SIZE' => $filesize, 'FORMAT' => $format)));
            }
            else
            {
                switch ($compare)
                {
                    case ComparisonCondition::GREATER_THAN :
                        $html[] = $this->renderParameter(
                            $filesize_id, 
                            Translation::get(
                                'FilesizeGreaterThanShort', 
                                array('SIZE' => $filesize, 'FORMAT' => $format)));
                        break;
                    case ComparisonCondition::LESS_THAN :
                        $html[] = $this->renderParameter(
                            $filesize_id, 
                            Translation::get('FilesizeLessThanShort', array('SIZE' => $filesize, 'FORMAT' => $format)));
                        break;
                }
            }
        }
        
        if ($filter_data->has_filter_property(FilterData::FILTER_EXTENSION))
        {
            $extension = strtoupper($filter_data->get_filter_property(FilterData::FILTER_EXTENSION));
            
            $html[] = $this->renderParameter(
                $this->get_parameter_name(FilterData::FILTER_EXTENSION), 
                Translation::get('TypeFile', array('EXTENSION' => $extension)));
        }
        elseif ($filter_data->has_filter_property(FilterData::FILTER_EXTENSION_TYPE))
        {
            $extension_type = $filter_data->get_filter_property(FilterData::FILTER_EXTENSION_TYPE);
            
            $html[] = $this->renderParameter(
                $this->get_parameter_name(FilterData::FILTER_EXTENSION_TYPE), 
                FileType::get_type_string($extension_type));
        }
        
        return implode(PHP_EOL, $html);
    }
}