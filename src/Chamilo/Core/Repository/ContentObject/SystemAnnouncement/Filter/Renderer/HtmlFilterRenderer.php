<?php
namespace Chamilo\Core\Repository\ContentObject\SystemAnnouncement\Filter\Renderer;

use Chamilo\Core\Repository\ContentObject\SystemAnnouncement\Filter\FilterData;
use Chamilo\Core\Repository\ContentObject\SystemAnnouncement\Storage\DataClass\SystemAnnouncement;
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
        
        $html[] = parent::add_properties();
        
        if ($filter_data->has_filter_property(FilterData::FILTER_ICON))
        {
            $translation = Translation::get(
                'IconSearchParameter', 
                array(
                    'ICON' => SystemAnnouncement::icon_name(
                        $filter_data->get_filter_property(FilterData::FILTER_ICON))));
            
            $html[] = $this->renderParameter($this->get_parameter_name(FilterData::FILTER_ICON), $translation);
        }
        
        return implode(PHP_EOL, $html);
    }
}