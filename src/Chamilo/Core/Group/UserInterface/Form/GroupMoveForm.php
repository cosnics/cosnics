<?php
namespace Chamilo\Core\Group\UserInterface\Form;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\UserInterface\Menu\GroupMenu;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;

/**
 * @package Chamilo\Core\Group\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupMoveForm extends FormValidator
{
    public const PROPERTY_LOCATION = 'location';

    private Group $group;

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \QuickformException
     */
    public function buildForm(): void
    {
        $this->addElement('select', self::PROPERTY_LOCATION, $this->getTranslation('NewLocation', [], Manager::CONTEXT),
            $this->getGroups());
        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', $this->getTranslation('Move'), null, null, new FontAwesomeGlyph('move')
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getGroups(): array
    {
        $group = $this->group;

        $urlFormat = $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_BROWSE_GROUPS,
                Manager::PARAM_GROUP_ID => '%s'
            ]
        );

        $groupMenu = new GroupMenu($group, $urlFormat, true, true);
        $renderer = new OptionsMenuRenderer();
        $groupMenu->render($renderer, 'sitemap');

        return $renderer->toArray();
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

    public function setDefaults(array $defaultValues = [], $filter = null)
    {
        $group = $this->group;
        $defaults[self::PROPERTY_LOCATION] = $group->getParentId();
        parent::setDefaults($defaults);
    }
}
