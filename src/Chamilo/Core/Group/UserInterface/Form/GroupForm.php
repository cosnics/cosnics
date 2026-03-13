<?php
namespace Chamilo\Core\Group\UserInterface\Form;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Domain\NestedSet;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\FormValidator;
use Chamilo\Libraries\UserInterface\Tree\Service\OptionsTreeRenderer;
use HTML_QuickForm_hidden;
use HTML_QuickForm_Rule_Required;
use HTML_QuickForm_select;
use HTML_QuickForm_text;

/**
 * @package Chamilo\Core\Group\UserInterface\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupForm extends FormValidator
{
    public const string RESULT_ERROR = 'GroupUpdateFailed';
    public const string RESULT_SUCCESS = 'GroupUpdated';
    public const string TYPE_CREATE = 'create';
    public const string TYPE_EDIT = 'edit';

    private string $formType;

    private Group $group;

    /**
     * @throws \QuickformException
     */
    public function __construct(string $formType, Group $group, string $action)
    {
        parent::__construct('groups_settings', self::FORM_METHOD_POST, $action);

        $this->group = $group;
        $this->formType = $formType;

        if ($this->formType == self::TYPE_EDIT) {
            $this->buildEditingForm();
        }
        elseif ($this->formType == self::TYPE_CREATE) {
            $this->buildCreationForm();
        }

        $this->setDefaults();
    }

    /**
     * @throws \QuickformException
     */
    public function buildBasicForm(): void
    {
        $this->addElement(
            HTML_QuickForm_text::class, Group::PROPERTY_NAME, $this->getTranslation('Name', [], Manager::CONTEXT),
            ['size' => '50']
        );
        $this->addRule(
            Group::PROPERTY_NAME, $this->getTranslation('ThisFieldIsRequired'), HTML_QuickForm_Rule_Required::class
        );

        $this->addElement(
            HTML_QuickForm_text::class, Group::PROPERTY_CODE, $this->getTranslation('Code', [], Manager::CONTEXT),
            ['size' => '50']
        );
        $this->addRule(
            Group::PROPERTY_CODE, $this->getTranslation('ThisFieldIsRequired'), HTML_QuickForm_Rule_Required::class
        );

        $this->addElement(
            HTML_QuickForm_select::class, NestedSet::PROPERTY_PARENT_ID,
            $this->getTranslation('Location', [], Manager::CONTEXT), $this->getGroupOptionsTreeRenderer()->getOptions()
        );
        $this->addRule(
            NestedSet::PROPERTY_PARENT_ID, $this->getTranslation('ThisFieldIsRequired'),
            HTML_QuickForm_Rule_Required::class
        );

        $this->addHtmlEditor(
            Group::PROPERTY_DESCRIPTION, $this->getTranslation('Description'), false
        );
    }

    /**
     * @throws \QuickformException
     */
    public function buildCreationForm(): void
    {
        $this->buildBasicForm();

        $this->addSaveResetButtons();
    }

    /**
     * @throws \QuickformException
     */
    public function buildEditingForm(): void
    {
        $this->buildBasicForm();
        $this->addElement(HTML_QuickForm_hidden::class, DataClass::PROPERTY_ID);
        $this->addSaveResetButtons();
    }

    /**
     * @throws \Throwable
     * @throws \QuickformException
     */
    public function createGroupFromForm(): bool
    {
        $group = $this->group;
        $values = $this->exportValues();

        $group->setName($values[Group::PROPERTY_NAME]);
        $group->setDescription($values[Group::PROPERTY_DESCRIPTION]);
        $group->setCode($values[Group::PROPERTY_CODE]);
        $group->setParentId($values[NestedSet::PROPERTY_PARENT_ID]);

        return $this->getGroupService()->createGroup($group, $this->getExecutingUser());
    }

    protected function getExecutingUser(): ?User
    {
        /**
         * @var \Chamilo\Core\User\Storage\DataClass\User
         */
        return $this->getService('Chamilo\Core\User\CurrentUser');
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    /**
     * @param class-string<\Chamilo\Libraries\UserInterface\Tree\Service\OptionsTreeRenderer> $className
     */
    public function getGroupOptionsTreeRenderer(
        string $className = 'Chamilo\Core\Group\UserInterface\Menu\GroupOptionsTreeRenderer'
    ): OptionsTreeRenderer
    {
        return $this->getService($className);
    }

    /**
     * @throws \QuickformException
     */
    public function setDefaults(array $defaultValues = [], $filter = null): void
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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \QuickformException
     */
    public function updateGroup(?User $executingUser = null): bool
    {
        $group = $this->group;
        $values = $this->exportValues();

        $group->setName($values[Group::PROPERTY_NAME]);
        $group->setDescription($values[Group::PROPERTY_DESCRIPTION]);
        $group->setCode($values[Group::PROPERTY_CODE]);

        if (!$this->getGroupService()->updateGroup($group, $executingUser)) {
            return false;
        }

        $newParentGroupIdentifier = $values[NestedSet::PROPERTY_PARENT_ID];

        if ($group->getParentId() != $newParentGroupIdentifier) {
            return $this->getGroupService()->moveGroup($group, $newParentGroupIdentifier, $this->getExecutingUser());
        }

        return true;
    }
}
