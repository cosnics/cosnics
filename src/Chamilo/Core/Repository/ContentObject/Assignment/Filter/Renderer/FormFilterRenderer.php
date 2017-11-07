<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Filter\Renderer;

use Chamilo\Core\Repository\ContentObject\Assignment\Filter\FilterData;
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
        
        // Start date
        $form_validator->addElement('category', Translation::get('StartTime'));
        $start_time = array();
        $start_time[] = $form_validator->createElement(
            'static', 
            '', 
            '', 
            '<span style="display:inline-block; margin-right: 2px;">' . Translation::get('From') . '</span>');
        $start_time[] = $form_validator->createElement(
            'text', 
            FilterData::FILTER_FROM_DATE, 
            Translation::get('From'), 
            'id="start_time_from" style="width:60px;"');
        $start_time[] = $form_validator->createElement(
            'static', 
            '', 
            '', 
            '<span style="display:inline-block; margin-left: 2px; margin-right: 2px;">' . Translation::get('To') .
                 '</span>');
        $start_time[] = $form_validator->createElement(
            'text', 
            FilterData::FILTER_TO_DATE, 
            Translation::get('To'), 
            'id="start_time_to" style="width:60px;"');
        $form_validator->addGroup($start_time, FilterData::FILTER_START_TIME);
        $form_validator->addElement('category');
        
        $renderer->setGroupElementTemplate('{element}', FilterData::FILTER_START_TIME);
        
        // End date
        $form_validator->addElement('category', Translation::get('EndTime'));
        $end_time = array();
        $end_time[] = $form_validator->createElement(
            'static', 
            '', 
            '', 
            '<span style="display:inline-block; margin-right: 2px;">' . Translation::get('From') . '</span>');
        $end_time[] = $form_validator->createElement(
            'text', 
            FilterData::FILTER_FROM_DATE, 
            Translation::get('From'), 
            'id="end_time_from" style="width:60px;"');
        $end_time[] = $form_validator->createElement(
            'static', 
            '', 
            '', 
            '<span style="display:inline-block; margin-left: 2px; margin-right: 2px;">' . Translation::get('To') .
                 '</span>');
        $end_time[] = $form_validator->createElement(
            'text', 
            FilterData::FILTER_TO_DATE, 
            Translation::get('To'), 
            'id="end_time_to" style="width:60px;"');
        $form_validator->addGroup($end_time, FilterData::FILTER_END_TIME);
        $form_validator->addElement('category');
        
        $renderer->setGroupElementTemplate('{element}', FilterData::FILTER_END_TIME);
    }

    /**
     *
     * @see \core\repository\filter\renderer\FormFilterRenderer::set_defaults()
     */
    public function set_defaults($defaults = array())
    {
        $filter_data = $this->get_filter_data();
        
        $start_time = $filter_data->get_filter_property(FilterData::FILTER_START_TIME);
        $end_time = $filter_data->get_filter_property(FilterData::FILTER_END_TIME);
        
        $defaults[FilterData::FILTER_START_TIME][FilterData::FILTER_FROM_DATE] = $start_time[FilterData::FILTER_FROM_DATE];
        $defaults[FilterData::FILTER_START_TIME][FilterData::FILTER_TO_DATE] = $start_time[FilterData::FILTER_TO_DATE];
        $defaults[FilterData::FILTER_END_TIME][FilterData::FILTER_FROM_DATE] = $end_time[FilterData::FILTER_FROM_DATE];
        $defaults[FilterData::FILTER_END_TIME][FilterData::FILTER_TO_DATE] = $end_time[FilterData::FILTER_TO_DATE];
        
        parent::set_defaults($defaults);
    }
}