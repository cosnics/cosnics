<?php
namespace Chamilo\Core\Repository\Publication\Location;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\Publication\LocationSupport;
use Chamilo\Core\Repository\Publication\Manager;

/**
 *
 * @package core\repository\publication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class LocationSelector
{

    /**
     *
     * @var \libraries\format\FormValidator
     */
    private $form_validator;

    /**
     *
     * @var \core\repository\publication\Locations
     */
    private $locations;

    /**
     *
     * @param \libraries\format\FormValidator $form_validator
     * @param \core\repository\publication\Locations $locations
     */
    function __construct($form_validator, $locations)
    {
        $this->form_validator = $form_validator;
        $this->locations = $locations;
    }

    /**
     *
     * @return \libraries\format\FormValidator
     */
    public function get_form_validator()
    {
        return $this->form_validator;
    }

    /**
     *
     * @param \libraries\format\FormValidator $form_validator
     */
    public function set_form_validator($form_validator)
    {
        $this->form_validator = $form_validator;
    }

    public function get_locations()
    {
        return $this->locations;
    }

    /**
     *
     * @param \core\repository\publication\Locations $locations
     */
    public function set_locations($locations)
    {
        $this->locations = $locations;
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
     * @return string
     */
    abstract public function get_header();

    /**
     *
     * @param Location $location
     * @return \HTML_QuickForm_element[]
     */
    abstract public function get_group(LocationSupport $location);
}