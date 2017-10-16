<?php
namespace Chamilo\Core\Group\Form;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package groups.lib.forms
 */
class GroupUserSearchForm extends FormValidator
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
     * Search in whole repository
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
     * The repository manager in which this search form is used
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
     * @param RepositoryManager $manager The repository manager in which this search form will be displayed
     * @param string $url The location to which the search request should be posted.
     */
    public function UserSearchForm($manager, $url)
    {
        parent::__construct(self::FORM_NAME, 'post', $url);
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
        $this->renderer->setElementTemplate('{element}');
        $this->frozen_elements[] = $this->addElement(
            'text',
            self::PARAM_SIMPLE_SEARCH_QUERY,
            Translation::get('Search', null, Utilities::COMMON_LIBRARIES),
            'size="20" class="search_query"');
        $this->addElement('submit', 'search', Translation::get('Ok', null, Utilities::COMMON_LIBRARIES));
    }

    /**
     * Display the form
     */
    public function display()
    {
        $html = array();
        $html[] = '<div class="simple_search" style="float:right; text-align: right; margin-bottom: 1em;">';
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

        if (isset($query) && $query != '')
        {
            $conditions = array();
            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_USERNAME),
                '*' . $values[self::PARAM_SIMPLE_SEARCH_QUERY] . '*');
            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME),
                '*' . $values[self::PARAM_SIMPLE_SEARCH_QUERY] . '*');
            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME),
                '*' . $values[self::PARAM_SIMPLE_SEARCH_QUERY] . '*');

            return new OrCondition($conditions);
        }
        else
        {
            return null;
        }
    }

    /**
     * Determines if the user is currently searching the repository.
     *
     * @return boolean True if the user is searching.
     */
    public function validate()
    {
        return (count($this->get_search_conditions()) > 0);
    }
}
