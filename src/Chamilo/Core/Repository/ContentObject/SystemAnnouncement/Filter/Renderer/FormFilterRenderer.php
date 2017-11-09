<?php
namespace Chamilo\Core\Repository\ContentObject\SystemAnnouncement\Filter\Renderer;

use Chamilo\Core\Repository\ContentObject\SystemAnnouncement\Filter\FilterData;
use Chamilo\Core\Repository\ContentObject\SystemAnnouncement\Storage\DataClass\SystemAnnouncement;
use Chamilo\Libraries\Translation\Translation;

/**
 * Render the parameters set via FilterData as a FormValidator
 * 
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FormFilterRenderer extends \Chamilo\Core\Repository\Filter\Renderer\FormFilterRenderer
{

    /**
     *
     * @see \core\repository\filter\renderer\FormFilterRenderer::build()
     */
    public function build()
    {
        parent::build();
        
        $form_validator = $this->get_form_validitor();
        $renderer = $this->get_renderer();
        
        $form_validator->addElement('category', Translation::get('Icon'));
        $options = SystemAnnouncement::get_possible_icons();
        asort($options);
        $options = array(0 => '-- ' . Translation::get('SelectIcon') . ' --') + $options;
        $form_validator->addElement('select', FilterData::FILTER_ICON, Translation::get('Icon'), $options);
        
        $form_validator->addElement('category');
    }

    /**
     *
     * @see \core\repository\filter\renderer\FormFilterRenderer::set_defaults()
     */
    public function set_defaults($defaults = array())
    {
        $filter_data = $this->get_filter_data();
        
        $defaults[FilterData::FILTER_ICON] = $filter_data->get_filter_property(FilterData::FILTER_ICON);
        parent::set_defaults($defaults);
    }
}