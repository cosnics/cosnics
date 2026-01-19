<?php
namespace Chamilo\Core\Group\UserInterface\Form;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\UserInterface\Menu\GroupMenu;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\NestedSet;

/**
 * @package Chamilo\Core\Group\UserInterface\Form
 */
class GroupForm extends FormValidator
{
    public const RESULT_ERROR = 'GroupUpdateFailed';
    public const RESULT_SUCCESS = 'GroupUpdated';

    public const TYPE_CREATE = 'create';
    public const TYPE_EDIT = 'edit';

    private string $form_type;

    private Group $group;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \QuickformException
     */
    public function __construct(string $form_type, Group $group, string $action)
    {
        parent::__construct('groups_settings', self::FORM_METHOD_POST, $action);

        $this->group = $group;
        $this->form_type = $form_type;
        if ($this->form_type == self::TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self::TYPE_CREATE)
        {
            $this->build_creation_form();
        }

        $this->setDefaults();
    }

    /**
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function build_basic_form(): void
    {
        $this->addElement('text', Group::PROPERTY_NAME, $this->getTranslation('Name', [], Manager::CONTEXT),
            ['size' => '50']);
        $this->addRule(
            Group::PROPERTY_NAME, $this->getTranslation('ThisFieldIsRequired'), 'required'
        );

        $this->addElement('text', Group::PROPERTY_CODE, $this->getTranslation('Code', [], Manager::CONTEXT),
            ['size' => '50']);
        $this->addRule(
            Group::PROPERTY_CODE, $this->getTranslation('ThisFieldIsRequired'), 'required'
        );

        $this->addElement(
            'select', NestedSet::PROPERTY_PARENT_ID, $this->getTranslation('Location', [], Manager::CONTEXT),
            $this->get_groups()
        );
        $this->addRule(
            NestedSet::PROPERTY_PARENT_ID, $this->getTranslation('ThisFieldIsRequired'), 'required'
        );

        $this->add_html_editor(
            Group::PROPERTY_DESCRIPTION, $this->getTranslation('Description'), false
        );
    }

    /**
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function build_creation_form(): void
    {
        $this->build_basic_form();

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', $this->getTranslation('Create')
        );
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', $this->getTranslation('Reset')
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function build_editing_form(): void
    {
        $this->build_basic_form();

        $this->addElement('hidden', DataClass::PROPERTY_ID);

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', $this->getTranslation('Update'), null, null,
            new FontAwesomeGlyph('arrow-right')
        );
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', $this->getTranslation('Reset')
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * @throws \Throwable
     * @throws \QuickformException
     */
    public function create_group(): bool
    {
        $group = $this->group;
        $values = $this->exportValues();

        $group->set_name($values[Group::PROPERTY_NAME]);
        $group->set_description($values[Group::PROPERTY_DESCRIPTION]);
        $group->set_code($values[Group::PROPERTY_CODE]);
        $group->setParentId($values[NestedSet::PROPERTY_PARENT_ID]);

        return $this->getGroupService()->createGroup($group);
    }

    public function get_group(): Group
    {
        return $this->group;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function get_groups(): array
    {
        $group = $this->group;

        $group_menu = new GroupMenu($group->getId(), null, true, true, true);
        $renderer = new OptionsMenuRenderer();
        $group_menu->render($renderer, 'sitemap');

        return $renderer->toArray();
    }

    /**
     * @throws \QuickformException
     */
    public function setDefaults(array $defaultValues = [], $filter = null)
    {
        $group = $this->group;

        $defaults[DataClass::PROPERTY_ID] = $group->getId();
        $defaults[NestedSet::PROPERTY_PARENT_ID] = $group->getParentId();
        $defaults[Group::PROPERTY_NAME] = $group->get_name();
        $defaults[Group::PROPERTY_CODE] = $group->get_code();
        $defaults[Group::PROPERTY_DESCRIPTION] = $group->get_description();

        parent::setDefaults($defaults);
    }

    /**
     * @return bool
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageNoResultException
     * @throws \QuickformException
     * @throws \Throwable
     */
    public function update_group(): bool
    {
        $group = $this->group;
        $values = $this->exportValues();

        $group->set_name($values[Group::PROPERTY_NAME]);
        $group->set_description($values[Group::PROPERTY_DESCRIPTION]);
        $group->set_code($values[Group::PROPERTY_CODE]);

        if (!$this->getGroupService()->updateGroup($group))
        {
            return false;
        }

        $newParentGroupIdentifier = $values[NestedSet::PROPERTY_PARENT_ID];

        if ($group->getParentId() != $newParentGroupIdentifier)
        {
            return $this->getGroupService()->moveGroup($group, $newParentGroupIdentifier);
        }

        return true;
    }
}
