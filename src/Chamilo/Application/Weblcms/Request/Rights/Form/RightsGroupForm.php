<?php
namespace Chamilo\Application\Weblcms\Request\Rights\Form;

use Chamilo\Application\Weblcms\Request\Rights\Rights;
use Chamilo\Application\Weblcms\Request\Rights\Storage\DataClass\RightsLocationEntityRightGroup;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\Parameters\RetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class RightsGroupForm extends FormValidator
{
    const PROPERTY_ACCESS = 'access';
    const PROPERTY_TARGETS = 'targets';

    private $form_user;

    function __construct($form_user, $action)
    {
        parent::__construct('rights', self::FORM_METHOD_POST, $action);

        $this->form_user = $form_user;
        $this->build_form();
        $this->setDefaults();
    }

    function build_form()
    {
        $element_template = [];
        $element_template[] = '<div class="form-row">';
        $element_template[] =
            '<div class="element"><!-- BEGIN error --><small class="text-danger">{error}</small><br /><!-- END error -->	{element}</div>';
        $element_template[] = '<div class="form_feedback"></div>';
        $element_template[] = '<div class="clearfix"></div>';
        $element_template[] = '</div>';
        $element_template = implode(PHP_EOL, $element_template);

        $this->addElement('category', Translation::get('RightsGroupAccess'));
        $types = new AdvancedElementFinderElementTypes();
        $types->add_element_type(UserEntity::getElementFinderTypeInstance());
        $types->add_element_type(PlatformGroupEntity::getElementFinderTypeInstance());
        $this->addElement('advanced_element_finder', self::PROPERTY_ACCESS, null, $types);
        $this->get_renderer()->setElementTemplate($element_template, self::PROPERTY_ACCESS);

        $this->addElement('category', Translation::get('RightsGroupTargets'));
        $types = new AdvancedElementFinderElementTypes();
        $types->add_element_type(PlatformGroupEntity::getElementFinderTypeInstance());
        $this->addElement('advanced_element_finder', self::PROPERTY_TARGETS, null, $types);
        $this->get_renderer()->setElementTemplate($element_template, self::PROPERTY_TARGETS);

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', Translation::get('Save', null, StringUtilities::LIBRARIES), null, null,
            new FontAwesomeGlyph('floppy-save')
        );
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', Translation::get('Reset', null, StringUtilities::LIBRARIES)
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function setDefaults($defaultValues = [], $filter = null)
    {
        /*
         * $default_elements = new AdvancedElementFinderElements(); $targets_entities = Rights ::
         * get_instance()->get_request_targets_entities(); $user_entity = UserEntity::getInstance(); $group_entity =
         * PlatformGroupEntity::getInstance(); foreach ($targets_entities[UserEntity::ENTITY_TYPE] as $entity) {
         * $default_elements->add_element($user_entity->get_element_finder_element($entity)); } foreach
         * ($targets_entities[PlatformGroupEntity::ENTITY_TYPE] as $entity) {
         * $default_elements->add_element($group_entity->get_element_finder_element($entity)); } $this->getElement(self
         *::PROPERTY_ACCESS)->setDefaultValues($default_elements);
         */
        parent::setDefaults($defaultValues, $filter);
    }

    function set_rights()
    {
        $values = $this->exportValues();

        $rights_util = Rights::getInstance();
        $location = $rights_util->get_request_root();

        $targets_entities = Rights::getInstance()->get_request_targets_entities();
        $location_id = $location->get_id();

        foreach ($values[self::PROPERTY_ACCESS] as $entity_type => $target_ids)
        {
            $to_add = array_diff($target_ids, (array) $targets_entities[$entity_type]);

            foreach ($to_add as $target_id)
            {
                if (!$rights_util->invert_request_location_entity_right(
                    Rights::VIEW_RIGHT, $target_id, $entity_type, $location_id
                ))
                {
                    return false;
                }
            }

            foreach ($target_ids as $target_id)
            {
                $location_entity_right = Rights::getInstance()->get_request_location_entity_right(
                    $target_id, $entity_type
                );

                foreach ($values[self::PROPERTY_TARGETS][PlatformGroupEntity::ENTITY_TYPE] as $group_id)
                {
                    $conditions = [];
                    $conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(
                            RightsLocationEntityRightGroup::class,
                            RightsLocationEntityRightGroup::PROPERTY_LOCATION_ENTITY_RIGHT_ID
                        ), new StaticConditionVariable($location_entity_right->get_id())
                    );
                    $conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(
                            RightsLocationEntityRightGroup::class,
                            RightsLocationEntityRightGroup::PROPERTY_GROUP_ID
                        ), new StaticConditionVariable($group_id)
                    );
                    $condition = new AndCondition($conditions);

                    $existing_right_group = DataManager::retrieve(
                        RightsLocationEntityRightGroup::class, new RetrieveParameters($condition)
                    );

                    if (!$existing_right_group instanceof RightsLocationEntityRightGroup)
                    {
                        $new_right_group = new RightsLocationEntityRightGroup();
                        $new_right_group->set_location_entity_right_id($location_entity_right->get_id());
                        $new_right_group->set_group_id($group_id);

                        if (!$new_right_group->create())
                        {
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }
}