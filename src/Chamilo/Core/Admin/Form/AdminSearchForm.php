<?php
namespace Chamilo\Core\Admin\Form;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package admin
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class AdminSearchForm extends FormValidator
{
    /**
     * #@+ Search parameter
     */
    const PARAM_SIMPLE_SEARCH_QUERY = 'query';
    const PARAM_TITLE_SEARCH_QUERY = 'title_matches';
    const PARAM_DESCRIPTION_SEARCH_QUERY = 'description_matches';
    const PARAM_SEARCH_SCOPE = 'scope';
    /**
     * #@-
     */
    /**
     * Search in whole application
     */
    const SEARCH_SCOPE_REPOSITORY = 0; // default
    /**
     * Search in current category
     */
    const SEARCH_SCOPE_CATEGORY = 1;
    /**
     * Search in current category and subcategories
     */
    const SEARCH_SCOPE_CATEGORY_AND_SUBCATEGORIES = 2;
    /**
     * Name of the search form
     */
    const FORM_NAME = 'search';

    /**
     * The manager in which this search form is used
     */
    private $manager;

    /**
     * Array holding the frozen elements in this search form
     */
    private $frozen_elements;

    /**
     * The renderer used to display the form
     */
    private $renderer;

    /**
     * Advanced or simple search form
     */
    private $advanced;

    /**
     * Creates a new search form
     *
     * @param $manager The admin manager in which this search form will be displayed
     * @param $url string The location to which the search request should be posted.
     */
    public function __construct($manager, $url, $form_id = '')
    {
        parent::__construct(self::FORM_NAME . $form_id, 'post', $url);
        $this->updateAttributes(array('id' => self::FORM_NAME . $form_id));
        $this->renderer = clone $this->defaultRenderer();
        $this->manager = $manager;
        $this->frozen_elements = array();

        $this->build_simple_search_form();

        $this->autofreeze();
        $this->accept($this->renderer);
    }

    /**
     * Gets the frozen element values
     *
     * @return array
     */
    public function get_frozen_values()
    {
        $values = array();
        foreach ($this->frozen_elements as $element)
        {
            $values[$element->getName()] = $element->getValue();
        }

        return $values;
    }

    /**
     * Freezes the elements defined in $frozen_elements
     */
    private function autofreeze()
    {
        if ($this->validate())
        {
            return;
        }
        foreach ($this->frozen_elements as $element)
        {
            $element->setValue(Request::get($element->getName()));
        }
    }

    /**
     * Build the simple search form.
     */
    private function build_simple_search_form()
    {
        $this->renderer->setFormTemplate(
            '<form {attributes}><div class="admin_search_form">{content}</div><div class="clear">&nbsp;</div></form>'
        );
        $this->renderer->setElementTemplate('<div class="form-row"><div class="formw">{element}</div></div>');

        $this->frozen_elements[] = $this->addElement(
            'text', self::PARAM_SIMPLE_SEARCH_QUERY, Translation::get('Search'), 'size="20"'
        );
        $this->addElement(
            'style_submit_button', 'submit', Translation::get('Search'), null, null, new FontAwesomeGlyph('search')
        );
    }

    /**
     * Display the form
     */
    public function render()
    {
        $html = array();
        $html[] = '<div class="admin_search">';
        $html[] = $this->renderer->toHTML();
        $html[] = '</div>';

        return implode('', $html);
    }

    /**
     * Get the search condition
     *
     * @return Condition The search condition
     */
    public function get_condition()
    {
        return $this->get_search_conditions();
    }

    /**
     * Gets the conditions that this form introduces.
     *
     * @return array The conditions.
     */
    private function get_search_conditions()
    {
        $values = $this->exportValues();
        $query = $values[self::PARAM_SIMPLE_SEARCH_QUERY];

        return null;
    }

    /**
     * Determines if the user is currently searching from the admin.
     *
     * @return boolean True if the user is searching.
     */
    public function validate()
    {
        return (count($this->get_search_conditions()) > 0);
    }
}
