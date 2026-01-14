<?php
namespace Chamilo\Core\Group\UserInterface\Form;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\UserInterface\Menu\GroupMenu;
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
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \QuickformException
     */
    public function __construct(Group $group, $action)
    {
        parent::__construct('group_move', self::FORM_METHOD_POST, $action);
        $this->group = $group;

        $this->build_form();
        $this->setDefaults();
    }

    /**
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function build_form(): void
    {
        $this->addElement('select', self::PROPERTY_LOCATION, $this->getTranslation('NewLocation', [], Manager::CONTEXT),
            $this->get_groups());
        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', $this->getTranslation('Move'), null, null, new FontAwesomeGlyph('move')
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function get_groups(): array
    {
        $group = $this->group;

        $group_menu = new GroupMenu($group->getId(), null, true, true);
        $renderer = new OptionsMenuRenderer();
        $group_menu->render($renderer, 'sitemap');

        return $renderer->toArray();
    }

    /**
     * @throws \QuickformException
     */
    public function get_new_parent()
    {
        return $this->exportValue(self::PROPERTY_LOCATION);
    }

    /**
     * @throws \Throwable
     * @throws \QuickformException
     */
    public function move_group(): bool
    {
        return $this->getService(GroupService::class)->moveGroup($this->group, $this->get_new_parent());
    }

    public function setDefaults(array $defaultValues = [], $filter = null)
    {
        $group = $this->group;
        $defaults[self::PROPERTY_LOCATION] = $group->getParentId();
        parent::setDefaults($defaults);
    }
}
