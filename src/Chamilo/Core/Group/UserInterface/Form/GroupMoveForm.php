<?php
namespace Chamilo\Core\Group\UserInterface\Form;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\FormValidator;
use Chamilo\Libraries\UserInterface\Tree\Service\OptionsTreeRenderer;
use HTML_QuickForm_select;

/**
 * @package Chamilo\Core\Group\UserInterface\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupMoveForm extends FormValidator
{
    public const PROPERTY_LOCATION = 'location';

    private Group $group;

    /**
     * @throws \QuickformException
     */
    public function __construct(Group $group, $action)
    {
        parent::__construct('group_move', self::FORM_METHOD_POST, $action);
        $this->group = $group;

        $this->buildForm();
        $this->setDefaults();
    }

    /**
     * @throws \QuickformException
     */
    public function addNewLocationSelect(): HTML_QuickForm_select
    {
        return $this->addElement(
            HTML_QuickForm_select::class, self::PROPERTY_LOCATION,
            $this->getTranslation('NewLocation', [], Manager::CONTEXT),
            $this->getGroupOptionsTreeRenderer()->getOptions()
        );
    }

    /**
     * @throws \QuickformException
     */
    public function buildForm(): void
    {
        $selectElement = $this->addNewLocationSelect();
        $selectElement->disableOptionByValue($this->group->getId());

        $this->addSaveResetButtons();
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
    public function getNewParent()
    {
        return $this->exportValue(self::PROPERTY_LOCATION);
    }

    /**
     * @throws \Throwable
     * @throws \QuickformException
     */
    public function moveGroup(): bool
    {
        return $this->getService(GroupService::class)->moveGroup($this->group, $this->getNewParent());
    }

    public function setDefaults(array $defaultValues = [], $filter = null): void
    {
        $group = $this->group;
        $defaults[self::PROPERTY_LOCATION] = $group->getParentId();
        parent::setDefaults($defaults);
    }
}
