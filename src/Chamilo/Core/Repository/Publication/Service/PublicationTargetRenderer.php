<?php
namespace Chamilo\Core\Repository\Publication\Service;

use Chamilo\Libraries\Format\Form\FormValidator;

class PublicationTargetRenderer
{
    public function addHeaderToForm(FormValidator $form)
    {

    }

    public function addFooterToForm(FormValidator $form)
    {

    }

    public function addPublicationTargerToForm()
    {

    }

    public function run()
    {
        $form_validator = $this->get_form_validator();
        $locations = $this->get_locations();

        $table_header = array();
        $table_header[] = '<table class="table table-striped table-bordered table-hover table-responsive">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';

        if ($locations->size() > 1)
        {
            $table_header[] = '<th class="cell-stat-x2">';
            $table_header[] = '<div class="checkbox no-toggle-style">';
            $table_header[] = '<input class="select-all" type="checkbox" />';
            $table_header[] = '<label></label>';
            $table_header[] = '</div>';
        }
        else
        {
            $table_header[] = '<th class="cell-stat-x2"></th>';
        }

        $table_header[] = $this->get_header();
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';

        $form_validator->addElement('html', implode(PHP_EOL, $table_header));

        $renderer = $form_validator->defaultRenderer();

        foreach ($locations->get_locations() as $key => $location)
        {
            $group = array();

            $group[] = $form_validator->createElement(
                'checkbox',
                $this->get_checkbox_name($locations->get_package(), $location),
                null,
                null,
                null,
                $location->encode());

            foreach ($this->get_group($location) as $group_element)
            {
                $group[] = $group_element;
            }

            $form_validator->addGroup($group, 'test_' . $key, null, '', false);

            $renderer->setElementTemplate(
                '<tr class="' . ($key % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>',
                'test_' . $key);
            $renderer->setGroupElementTemplate('<td>{element}</td>', 'test_' . $key);
        }

        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $form_validator->addElement('html', implode(PHP_EOL, $table_footer));
    }

    /**
     *
     * @param string $context
     * @return string
     */
    public function get_checkbox_name($context, $location)
    {
        $registration = Configuration::registration($context);
        return Manager::WIZARD_LOCATION . '[' . $registration[Registration::PROPERTY_ID] . '][' .
            md5(serialize($location)) . ']';
    }

    /**
     *
     * @see \core\repository\publication\LocationRenderer::get_header()
     */
    public function get_header()
    {
        $table_header = array();
        $table_header[] = '<th>';
        $table_header[] = Translation::get('Location', null, Manager::context());
        $table_header[] = '</th>';

        return implode('', $table_header);
    }

    /**
     *
     * @see \core\repository\publication\LocationRenderer::get_group()
     */
    public function get_group(LocationSupport $location)
    {
        $form_validator = $this->get_form_validator();
        $group = array();

        $group[] = $form_validator->createElement('static', null, null, $location->get_name());

        return $group;
    }
}