<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'HTML_QuickForm_element_finder.php';

/**
 * AJAX-based tree search and multiselect element.
 * Use at your own risk.
 *
 * @package Chamilo\Libraries\Format\Form\Element
 * @author Tim De Pauw
 */
class HTML_QuickForm_user_group_finder extends HTML_QuickForm_element_finder
{

    /**
     *
     * @param string $elementName
     * @param string $elementLabel
     * @param string $search_url
     * @param string[] $locale
     * @param string[] $default_values
     * @param string[] $options
     */
    public function __construct($elementName = null, $elementLabel = null, $search_url = null,
        $locale = array('Display' => 'Display'), $default_values = array(), $options = array())
    {
        parent::__construct($elementName, $elementLabel, $search_url, $locale, $default_values, $options);
        $this->_type = 'user_group_finder';
    }

    /**
     *
     * @see HTML_QuickForm_element_finder::getValue()
     */
    public function getValue()
    {
        $results = array();
        $values = $this->get_active_elements();

        /**
         * Process the array values so we end up with a 2-dimensional array
         * Keys are the selection type, values are the selected objects
         */

        foreach ($values as $value)
        {
            $value = explode('_', $value['id']);

            if (! isset($results[$value[0]]) || ! is_array($results[$value[0]]))
            {
                $results[$value[0]] = array();
            }

            $results[$value[0]][] = $value[1];
        }

        return $results;
    }
}
