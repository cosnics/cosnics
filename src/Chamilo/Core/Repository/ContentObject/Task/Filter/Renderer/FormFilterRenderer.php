<?php
namespace Chamilo\Core\Repository\ContentObject\Task\Filter\Renderer;

use Chamilo\Core\Repository\ContentObject\Task\Filter\FilterData;
use Chamilo\Core\Repository\ContentObject\Task\Storage\DataClass\Task;
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
        $form_validator->addElement('category', Translation::get('StartDate'));
        $start_date = [];
        $start_date[] = $form_validator->createElement(
            'static', 
            '', 
            '', 
            '<span style="display:inline-block; margin-right: 2px;">' . Translation::get('From') . '</span>');
        $start_date[] = $form_validator->createElement(
            'text', 
            FilterData::FILTER_FROM_DATE, 
            Translation::get('From'), 
            'id="start_date_from" style="width:60px;"');
        $start_date[] = $form_validator->createElement(
            'static', 
            '', 
            '', 
            '<span style="display:inline-block; margin-left: 2px; margin-right: 2px;">' . Translation::get('To') .
                 '</span>');
        $start_date[] = $form_validator->createElement(
            'text', 
            FilterData::FILTER_TO_DATE, 
            Translation::get('To'), 
            'id="start_date_to" style="width:60px;"');
        $form_validator->addGroup($start_date, FilterData::FILTER_START_DATE);
        $form_validator->addElement('category');
        
        $renderer->setGroupElementTemplate('{element}', FilterData::FILTER_START_DATE);
        
        // End date
        $form_validator->addElement('category', Translation::get('EndDate'));
        $due_date = [];
        $due_date[] = $form_validator->createElement(
            'static', 
            '', 
            '', 
            '<span style="display:inline-block; margin-right: 2px;">' . Translation::get('From') . '</span>');
        $due_date[] = $form_validator->createElement(
            'text', 
            FilterData::FILTER_FROM_DATE, 
            Translation::get('From'), 
            'id="due_date_from" style="width:60px;"');
        $due_date[] = $form_validator->createElement(
            'static', 
            '', 
            '', 
            '<span style="display:inline-block; margin-left: 2px; margin-right: 2px;">' . Translation::get('To') .
                 '</span>');
        $due_date[] = $form_validator->createElement(
            'text', 
            FilterData::FILTER_TO_DATE, 
            Translation::get('To'), 
            'id="due_date_to" style="width:60px;"');
        $form_validator->addGroup($due_date, FilterData::FILTER_DUE_DATE);
        $form_validator->addElement('category');
        
        $renderer->setGroupElementTemplate('{element}', FilterData::FILTER_DUE_DATE);
        
        // Frequency
        $form_validator->addElement('category', Translation::get('RepeatType'));
        
        $options = [];
        $options[0] = '-- ' . Translation::get('SelectRepeatType') . ' --';
        $options[- 1] = Translation::get('NoRepeat');
        
        $frequency_options = Task::get_frequency_options();
        asort($frequency_options);
        
        $options = $options + $frequency_options;
        
        $form_validator->addElement('select', FilterData::FILTER_FREQUENCY, Translation::get('RepeatType'), $options);
        $form_validator->addElement('category');
        
        // Task type
        $form_validator->addElement('category', Translation::get('TaskType'));
        
        $options = [];
        $options[0] = '-- ' . Translation::get('SelectTaskType') . ' --';
        
        $types_options = Task::get_types_options();
        asort($types_options);
        
        $options = $options + $types_options;
        
        $form_validator->addElement('select', FilterData::FILTER_CATEGORY, Translation::get('TaskType'), $options);
        $form_validator->addElement('category');
        
        // Priority
        $form_validator->addElement('category', Translation::get('Priority'));
        
        $options = [];
        $options[0] = '-- ' . Translation::get('SelectPriority') . ' --';
        
        $priority_options = Task::get_priority_options();
        asort($priority_options);
        
        $options = $options + $priority_options;
        
        $form_validator->addElement('select', FilterData::FILTER_PRIORITY, Translation::get('Priority'), $options);
        $form_validator->addElement('category');
    }

    /**
     *
     * @see \core\repository\filter\renderer\FormFilterRenderer::set_defaults()
     */
    public function set_defaults($defaults = [])
    {
        $filter_data = $this->get_filter_data();
        
        $defaults[FilterData::FILTER_PRIORITY] = $filter_data->get_filter_property(FilterData::FILTER_PRIORITY);
        $defaults[FilterData::FILTER_CATEGORY] = $filter_data->get_filter_property(FilterData::FILTER_CATEGORY);
        $defaults[FilterData::FILTER_FREQUENCY] = $filter_data->get_filter_property(FilterData::FILTER_FREQUENCY);
        
        $start_date = $filter_data->get_filter_property(FilterData::FILTER_START_DATE);
        $due_date = $filter_data->get_filter_property(FilterData::FILTER_DUE_DATE);
        
        $defaults[FilterData::FILTER_START_DATE][FilterData::FILTER_FROM_DATE] = $start_date[FilterData::FILTER_FROM_DATE];
        $defaults[FilterData::FILTER_START_DATE][FilterData::FILTER_TO_DATE] = $start_date[FilterData::FILTER_TO_DATE];
        $defaults[FilterData::FILTER_DUE_DATE][FilterData::FILTER_FROM_DATE] = $due_date[FilterData::FILTER_FROM_DATE];
        $defaults[FilterData::FILTER_DUE_DATE][FilterData::FILTER_TO_DATE] = $due_date[FilterData::FILTER_TO_DATE];
        
        parent::set_defaults($defaults);
    }
}