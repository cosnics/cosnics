<?php
namespace Chamilo\Core\Repository\ContentObject\Webpage\Filter\Renderer;

use Chamilo\Core\Repository\ContentObject\Webpage\Filter\FilterData;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;

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
        parent :: build();
        
        $form_validator = $this->get_form_validitor();
        $renderer = $this->get_renderer();
        
        $form_validator->addElement('category', Translation :: get('Filesize'));
        
        $group = array();
        $group[] = $form_validator->createElement(
            'select', 
            FilterData :: FILTER_COMPARE, 
            '', 
            array(
                ComparisonCondition :: EQUAL => Translation :: get('AboutShort'), 
                ComparisonCondition :: GREATER_THAN => Translation :: get('GreaterThanShort'), 
                ComparisonCondition :: LESS_THAN => Translation :: get('LessThanShort')));
        $group[] = $form_validator->createElement('text', FilterData :: FILTER_FILESIZE, null, 'style="width: 50px;"');
        $group[] = $form_validator->createElement(
            'select', 
            FilterData :: FILTER_FORMAT, 
            '', 
            array(
                1 => Translation :: get('KilobyteShort'), 
                2 => Translation :: get('MegabyteShort'), 
                3 => Translation :: get('GigabyteShort')));
        $form_validator->addGroup($group, FilterData :: FILTER_FILESIZE, null, null, false);
        $renderer->setGroupElementTemplate('{element}', FilterData :: FILTER_FILESIZE);
        
        $form_validator->addElement('category');
    }

    /**
     *
     * @see \core\repository\filter\renderer\FormFilterRenderer::set_defaults()
     */
    public function set_defaults($defaults = array())
    {
        $filter_data = $this->get_filter_data();
        
        $defaults[FilterData :: FILTER_COMPARE] = $filter_data->get_filter_property(FilterData :: FILTER_COMPARE);
        $defaults[FilterData :: FILTER_FILESIZE] = $filter_data->get_filter_property(FilterData :: FILTER_FILESIZE);
        $defaults[FilterData :: FILTER_FORMAT] = $filter_data->get_filter_property(FilterData :: FILTER_FORMAT);
        parent :: set_defaults($defaults);
    }
}