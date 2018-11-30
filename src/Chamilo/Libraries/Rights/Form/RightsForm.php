<?php
namespace Chamilo\Libraries\Rights\Form;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Utilities\Utilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Rights\Form
 * @author Sven Vanpoucke
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsForm extends FormValidator
{
    const INHERIT_FALSE = 1;

    const INHERIT_TRUE = 0;

    const PROPERTY_BUTTONS = 'buttons';

    const PROPERTY_INHERIT = 'inherit';

    const PROPERTY_RESET = 'reset';

    const PROPERTY_RIGHT_OPTION = 'right_option';

    const PROPERTY_SUBMIT = 'submit';

    const PROPERTY_TARGETS = 'targets';

    const RIGHT_OPTION_ALL = 0;

    const RIGHT_OPTION_SELECT = 2;

    const RIGHT_OTPION_ME = 1;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * @var boolean
     */
    private $isAllowedToInherit;

    /**
     * @var integer[]
     */
    private $availableRights;

    /**
     * @var array
     */
    private $entities;

    /**
     * @param string $postBackUrl
     * @param \Symfony\Component\Translation\Translator $translator
     * @param boolean $isAllowedToInherit
     * @param integer[] $availableRights
     * @param array $entities
     */
    public function __construct(
        string $postBackUrl, Translator $translator, bool $isAllowedToInherit, array $availableRights, array $entities
    )
    {
        parent::__construct('simple_rights_editor', 'post', $postBackUrl);

        $this->translator = $translator;
        $this->isAllowedToInherit = $isAllowedToInherit;
        $this->entities = $entities;
        $this->availableRights = $availableRights;

        $this->buildForm();
        $this->setDefaults();
    }

    /**
     * @param string[] $defaultValues
     * @param string[] $filter
     *
     * @throws \Exception
     */
    public function setDefaults($defaultValues = null, $filter = null)
    {
        if ($this->isAllowedToInherit())
        {
            $defaultValues[self::PROPERTY_INHERIT] = self::INHERIT_TRUE;
        }
        else
        {
            $defaultValues[self::PROPERTY_INHERIT] = self::INHERIT_FALSE;
        }

        parent::setDefaults($defaultValues, $filter);
    }

    /**
     * Builds the form
     */
    public function buildForm()
    {
        $this->buildInheritanceForm();

        $this->addElement('html', '<div style="display:none;" class="specific_rights_selector_box">');

        foreach ($this->getAvailableRights() as $rightName => $rightIdentifier)
        {
            $this->buildRightForm($rightName, $rightIdentifier);
        }

        $this->addElement('html', '</div>');

        $this->buildFormFooter();
    }

    /**
     * Builds the form footer
     */
    private function buildFormFooter()
    {
        $translator = $this->getTranslator();

        $buttons = array();

        $buttons[] = $this->createElement(
            'style_submit_button', self::PROPERTY_SUBMIT, $translator->trans('Submit', [], Utilities::COMMON_LIBRARIES),
            null, null, 'arrow-right'
        );

        $buttons[] = $this->createElement(
            'style_reset_button', self::PROPERTY_RESET, $translator->trans('Reset', [], Utilities::COMMON_LIBRARIES)
        );

        $this->addGroup($buttons, self::PROPERTY_BUTTONS, null, '&nbsp;', false);

        $this->addElement(
            'html', ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Core\Rights\Editor', true) . 'RightsForm.js'
        )
        );
    }

    /**
     * Builds the inheritance form (wheter to inherit the rights from parent location or not)
     */
    private function buildInheritanceForm()
    {
        $translator = $this->getTranslator();

        $this->addElement('category', $translator->trans('Inheritance', [], 'Chamilo\Libraries\Rights'));

        $group = array();

        if ($this->isAllowedToInherit())
        {
            $group[] = &$this->createElement(
                'radio', null, null, $translator->trans('InheritRights', [], 'Chamilo\Libraries\Rights'),
                self::INHERIT_TRUE, array('class' => 'inherit_rights_selector')
            );
        }
        else
        {
            $group[] = &$this->createElement(
                'radio', null, null, $translator->trans('InheritRights', [], 'Chamilo\Libraries\Rights'),
                self::INHERIT_TRUE, array('class' => 'inherit_rights_selector', 'disabled' => 'disabled')
            );
        }

        $group[] = &$this->createElement(
            'radio', null, null, $translator->trans('UseSpecificRights', [], 'Chamilo\Libraries\Rights'),
            self::INHERIT_FALSE, array('class' => 'specific_rights_selector')
        );

        $this->addGroup($group, self::PROPERTY_INHERIT, null, '');

        $this->addElement('category');
    }

    /**
     * Builds the form for a given right
     *
     * @param string $rightName
     * @param integer $rightIdentifier
     */
    private function buildRightForm($rightName, $rightIdentifier)
    {
        $translator = $this->getTranslator();

        $name = self::PROPERTY_RIGHT_OPTION . '[' . $rightIdentifier . ']';

        $this->addElement('category', $rightName);
        $this->addElement('html', '<div class="right">');

        $group = array();

        $group[] = &$this->createElement(
            'radio', null, null, $translator->trans('Everyone', [], 'Chamilo\Libraries\Rights'), self::RIGHT_OPTION_ALL,
            array('class' => 'other_option_selected')
        );
        $group[] = &$this->createElement(
            'radio', null, null, $translator->trans('OnlyForMe', [], 'Chamilo\Libraries\Rights'), self::RIGHT_OTPION_ME,
            array('class' => 'other_option_selected')
        );
        $group[] = &$this->createElement(
            'radio', null, null, $translator->trans('SelectSpecificEntities', [], 'Chamilo\Libraries\Rights'),
            self::RIGHT_OPTION_SELECT, array('class' => 'entity_option_selected')
        );

        $this->addGroup($group, $name, '', '');

        // Add the advanced element finder
        $types = new AdvancedElementFinderElementTypes();

        foreach ($this->entities as $entity)
        {
            $types->add_element_type($entity->get_element_finder_type());
        }

        $this->addElement('html', '<div style="margin-left:25px; display:none;" class="entity_selector_box">');
        $this->addElement('advanced_element_finder', self::PROPERTY_TARGETS . '_' . $rightIdentifier, null, $types);

        $this->addElement('html', '</div></div>');

        $this->addElement('category');
    }

    /**
     * @return integer[]
     */
    public function getAvailableRights(): array
    {
        return $this->availableRights;
    }

    /**
     * @return array
     */
    public function getEntities(): array
    {
        return $this->entities;
    }

    /**
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @return bool
     */
    public function isAllowedToInherit(): bool
    {
        return $this->isAllowedToInherit;
    }
}
