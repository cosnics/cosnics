<?php
namespace Chamilo\Core\Group\UserInterface\Form;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\UserInterface\Menu\GroupMenu;
use Chamilo\Libraries\Architecture\Application\Application;
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

    private string $formType;

    private Group $group;

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \QuickformException
     */
    public function __construct(string $form_type, Group $group, string $action)
    {
        parent::__construct('groups_settings', self::FORM_METHOD_POST, $action);

        $this->group = $group;
        $this->formType = $form_type;

        if ($this->formType == self::TYPE_EDIT)
        {
            $this->buildEditingForm();
        }
        elseif ($this->formType == self::TYPE_CREATE)
        {
            $this->buildCreationForm();
        }

        $this->setDefaults();
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \QuickformException
     */
    public function buildBasicForm(): void
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
            $this->getGroupsOptions()
        );
        $this->addRule(
            NestedSet::PROPERTY_PARENT_ID, $this->getTranslation('ThisFieldIsRequired'), 'required'
        );

        $this->add_html_editor(
            Group::PROPERTY_DESCRIPTION, $this->getTranslation('Description'), false
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \QuickformException
     */
    public function buildCreationForm(): void
    {
        $this->buildBasicForm();

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', $this->getTranslation('Create')
        );
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', $this->getTranslation('Reset')
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \QuickformException
     */
    public function buildEditingForm(): void
    {
        $this->buildBasicForm();

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

        $group->setName($values[Group::PROPERTY_NAME]);
        $group->setDescription($values[Group::PROPERTY_DESCRIPTION]);
        $group->setCode($values[Group::PROPERTY_CODE]);
        $group->setParentId($values[NestedSet::PROPERTY_PARENT_ID]);

        return $this->getGroupService()->createGroup($group);
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    /**
     * @return array
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getGroupsOptions(): array
    {
        // TODO: Get groups as an indented flat list
        $group = $this->group;

        $urlFormat = $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_BROWSE_GROUPS,
                Manager::PARAM_GROUP_ID => '%s'
            ]
        );



        $group_menu = new GroupMenu($group, $urlFormat, true, true, true);
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
        $defaults[Group::PROPERTY_NAME] = $group->getName();
        $defaults[Group::PROPERTY_CODE] = $group->getCode();
        $defaults[Group::PROPERTY_DESCRIPTION] = $group->getDescription();

        parent::setDefaults($defaults);
    }

    /**
     * @return bool
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \QuickformException
     * @throws \Throwable
     */
    public function updateGroup(): bool
    {
        $group = $this->group;
        $values = $this->exportValues();

        $group->setName($values[Group::PROPERTY_NAME]);
        $group->setDescription($values[Group::PROPERTY_DESCRIPTION]);
        $group->setCode($values[Group::PROPERTY_CODE]);

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
