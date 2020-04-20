<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform\Form;

use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class AdminForm extends FormValidator
{
    const PROPERTY_ENTITIES = 'entities';
    const PROPERTY_TARGETS = 'targets';

    /**
     *
     * @var BrowserComponent
     */
    private $application;

    public function __construct($application, $action)
    {
        parent::__construct('admin', self::FORM_METHOD_POST, $action);

        $this->application = $application;
        $this->build_form();
        $this->setDefaults();
    }

    public function build_form()
    {
        $element_template = array();
        $element_template[] = '<div class="form-row">';
        $element_template[] =
            '<div class="element"><!-- BEGIN error --><small class="text-danger">{error}</small><br /><!-- END error -->	{element}</div>';
        $element_template[] = '<div class="form_feedback"></div>';
        $element_template[] = '<div class="clearfix"></div>';
        $element_template[] = '</div>';
        $element_template = implode(PHP_EOL, $element_template);

        $this->addElement('category', Translation::get('Entities'));

        $types = new AdvancedElementFinderElementTypes();

        foreach ($this->application->get_entity_types() as $type)
        {
            $types->add_element_type($type::get_element_finder_type());
        }

        $this->addElement('advanced_element_finder', self::PROPERTY_ENTITIES, null, $types);
        $this->get_renderer()->setElementTemplate($element_template, self::PROPERTY_ENTITIES);

        $this->addElement('category', Translation::get('Targets'));

        $types = new AdvancedElementFinderElementTypes();

        foreach ($this->application->get_target_types() as $type)
        {
            $types->add_element_type($type::get_element_finder_type());
        }

        $this->addElement('advanced_element_finder', self::PROPERTY_TARGETS, null, $types);
        $this->get_renderer()->setElementTemplate($element_template, self::PROPERTY_TARGETS);

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', Translation::get('Save', null, Utilities::COMMON_LIBRARIES), null, null,
            new FontAwesomeGlyph('floppy-save')
        );
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', Translation::get('Reset', null, Utilities::COMMON_LIBRARIES)
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
}
