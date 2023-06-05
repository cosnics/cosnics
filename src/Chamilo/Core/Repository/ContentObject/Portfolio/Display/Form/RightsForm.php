<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Form;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Form to configure the portfolio (sub)item rights
 *
 * @package repository\content_object\portfolio\display
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsForm extends FormValidator
{
    public const INHERIT_FALSE = 1;

    public const INHERIT_TRUE = 0;

    public const PROPERTY_BUTTONS = 'buttons';

    public const PROPERTY_INHERIT = 'inherit';

    public const PROPERTY_RESET = 'reset';

    public const PROPERTY_RIGHT_OPTION = 'right_option';

    public const PROPERTY_SUBMIT = 'submit';

    public const PROPERTY_TARGETS = 'targets';

    public const RIGHT_OPTION_ALL = 0;
    public const RIGHT_OPTION_ME = 1;
    public const RIGHT_OPTION_SELECT = 2;

    /**
     * The available rights
     *
     * @var int[string]
     */
    private $available_rights;

    /**
     * The configurable entities
     *
     * @var \core\rights\RightsEntity[]
     */
    private $entities;

    /**
     * The selected location ids
     *
     * @var \core\rights\RightLocation[]
     */
    private $locations;

    /**
     * The currently selected entities
     *
     * @var \core\rights\RightsLocationEntityRight[]
     */
    private $selected_entities;

    /**
     * Constructor
     *
     * @param string url
     * @param \core\rights\RightLocation[] $locations
     * @param int[string] $available_rights
     * @param \core\rights\RightsEntity[] $entities
     * @param \core\rights\RightsLocationEntityRight[] $selected_entities
     */
    public function __construct($url, $locations, $available_rights, $entities, $selected_entities)
    {
        parent::__construct('simple_rights_editor', self::FORM_METHOD_POST, $url);

        $this->locations = $locations;
        $this->entities = $entities;
        $this->available_rights = $available_rights;
        $this->selected_entities = $selected_entities;

        $this->build_form();

        $this->setDefaults();
    }

    /**
     * Builds the form
     */
    public function build_form()
    {
        $this->build_locations_form();
        $this->build_inheritance_form();

        $this->addElement('html', '<div style="display:none;" class="specific_rights_selector_box">');

        foreach ($this->available_rights as $right_name => $right_id)
        {
            $this->build_right_form($right_name, $right_id);
        }

        $this->addElement('html', '</div>');

        $this->build_form_footer();
    }

    /**
     * Builds the form footer
     */
    private function build_form_footer()
    {
        $buttons = [];

        $buttons[] = $this->createElement(
            'style_submit_button', self::PROPERTY_SUBMIT, Translation::get('Submit', null, StringUtilities::LIBRARIES),
            null, null, new FontAwesomeGlyph('arrow-right')
        );

        $buttons[] = $this->createElement(
            'style_reset_button', self::PROPERTY_RESET, Translation::get('Reset', null, StringUtilities::LIBRARIES)
        );

        $this->addGroup($buttons, self::PROPERTY_BUTTONS, null, '&nbsp;', false);

        $this->addElement(
            'html', $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\Portfolio\Display') .
            'RightsForm.js'
        )
        );
    }

    /**
     * Builds the inheritance form (wheter to inherit the rights from parent location or not)
     */
    private function build_inheritance_form()
    {
        $locations = $this->locations;
        $first_location = $locations[0];

        if ($first_location->get_parent_id())
        {
            $has_root_location = false;
        }
        else
        {
            $has_root_location = true;
        }

        $this->addElement('category', Translation::get('Inheritance'));

        $group = [];

        if (!$has_root_location)
        {
            $group[] = &$this->createElement(
                'radio', null, null, Translation::get('InheritRights'), self::INHERIT_TRUE,
                ['class' => 'inherit_rights_selector']
            );
        }
        else
        {
            $group[] = &$this->createElement(
                'radio', null, null, Translation::get('InheritRights'), self::INHERIT_TRUE,
                ['class' => 'inherit_rights_selector', 'disabled' => 'disabled']
            );
        }
        $group[] = &$this->createElement(
            'radio', null, null, Translation::get('UseSpecificRights'), self::INHERIT_FALSE,
            ['class' => 'specific_rights_selector']
        );

        $this->addGroup($group, self::PROPERTY_INHERIT, null, '');
    }

    /**
     * Build the locations form
     */
    private function build_locations_form()
    {
        if (count($this->locations) > 1)
        {
            $html = [];
            $html[] = '<ul>';

            foreach ($this->locations as $location)
            {
                $html[] = '<li>' . $location->get_node()->get_content_object()->get_title();
            }

            $html[] = '</ul>';

            $this->addElement('category', Translation::get('SelectedPortfolioItems'));
            $this->addElement('html', implode(PHP_EOL, $html));
        }
    }

    /**
     * Builds the form for a given right
     *
     * @param string $right_name
     * @param int $right_id
     */
    private function build_right_form($right_name, $right_id)
    {
        $name = self::PROPERTY_RIGHT_OPTION . '_' . $right_id;

        $this->addElement('category', $right_name);
        $this->addElement('html', '<div class="right">');

        $group = [];

        $group[] = &$this->createElement(
            'radio', null, null, Translation::get('Everyone'), self::RIGHT_OPTION_ALL,
            ['class' => 'other_option_selected']
        );
        $group[] = &$this->createElement(
            'radio', null, null, Translation::get('OnlyForMe'), self::RIGHT_OPTION_ME,
            ['class' => 'other_option_selected']
        );
        $group[] = &$this->createElement(
            'radio', null, null, Translation::get('SelectSpecificEntities'), self::RIGHT_OPTION_SELECT,
            ['class' => 'entity_option_selected']
        );

        $this->addGroup($group, $name, '', '');

        // Add the advanced element finder
        $types = new AdvancedElementFinderElementTypes();

        foreach ($this->entities as $entity)
        {
            $types->add_element_type($entity->getElementFinderType());
        }

        $this->addElement('html', '<div style="display:none;" class="entity_selector_box">');
        $this->addElement('advanced_element_finder', self::PROPERTY_TARGETS . '_' . $right_id, null, $types);

        $this->addElement('html', '</div></div>');
    }

    /**
     * Sets the default values for this form
     *
     * @param string[] $defaults
     */
    public function setDefaults($defaults = [], $filter = null)
    {
        $locations = $this->locations;
        $first_location = $locations[0];

        if ($first_location)
        {
            if ($first_location->inherits())
            {
                $defaults[self::PROPERTY_INHERIT] = self::INHERIT_TRUE;
                foreach ($this->available_rights as $right_id)
                {
                    $defaults[self::PROPERTY_RIGHT_OPTION . '_' . $right_id] = self::RIGHT_OPTION_ALL;
                }
            }
            else
            {
                $defaults[self::PROPERTY_INHERIT] = self::INHERIT_FALSE;
            }

            $selected_entities_per_right = [];
            foreach ($this->selected_entities as $selected_entity)
            {
                $selected_entities_per_right[$selected_entity->get_right_id()][] = $selected_entity;
            }

            foreach ($this->available_rights as $right_id)
            {
                if (count($selected_entities_per_right[$right_id]) >= 1)
                {
                    $selected_entity = $selected_entities_per_right[$right_id][0];
                    if ($selected_entity->get_entity_type() == 0 && $selected_entity->get_entity_id() == 0)
                    {
                        $defaults[self::PROPERTY_RIGHT_OPTION . '_' . $right_id] = self::RIGHT_OPTION_ALL;
                        continue;
                    }
                }

                if (count($selected_entities_per_right[$right_id]) == 1)
                {

                    if ($selected_entity->get_entity_type() == 1 &&
                        $selected_entity->get_entity_id() == $this->getSession()->get(Manager::SESSION_USER_IO))
                    {
                        $defaults[self::PROPERTY_RIGHT_OPTION . '_' . $right_id] = self::RIGHT_OPTION_ME;
                        continue;
                    }
                }

                $defaults[self::PROPERTY_RIGHT_OPTION . '_' . $right_id] = self::RIGHT_OPTION_SELECT;

                $default_elements = new AdvancedElementFinderElements();

                foreach ($selected_entities_per_right[$right_id] as $selected_entity)
                {
                    $entity = $this->entities[$selected_entity->get_entity_type()];

                    $elementFinderElement = $entity->get_element_finder_element($selected_entity->get_entity_id());

                    if ($elementFinderElement instanceof AdvancedElementFinderElement)
                    {
                        $default_elements->add_element($elementFinderElement);
                    }
                }

                $element = $this->getElement(self::PROPERTY_TARGETS . '_' . $right_id);
                $element->setDefaultValues($default_elements);
            }
        }

        parent::setDefaults($defaults);
    }
}
