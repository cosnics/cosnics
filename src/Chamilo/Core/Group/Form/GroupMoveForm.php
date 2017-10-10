<?php
namespace Chamilo\Core\Group\Form;

use Chamilo\Core\Group\Menu\GroupMenu;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package groups.lib.forms
 */
class GroupMoveForm extends FormValidator
{
    const PROPERTY_LOCATION = 'location';

    private $group;

    private $locations = array();

    private $level = 1;

    public function __construct($group, $action, $user)
    {
        parent::__construct('group_move', 'post', $action);
        $this->group = $group;

        $this->build_form();

        $this->setDefaults();
    }

    public function build_form()
    {
        $this->addElement('select', self::PROPERTY_LOCATION, Translation::get('NewLocation'), $this->get_groups());
        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation::get('Move', null, Utilities::COMMON_LIBRARIES),
            null,
            null,
            'move');

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function move_group()
    {
        return $this->group->move($this->get_new_parent());
    }

    public function get_new_parent()
    {
        return $this->exportValue(self::PROPERTY_LOCATION);
    }

    public function get_groups()
    {
        $group = $this->group;

        $group_menu = new GroupMenu($group->get_id(), null, true, true);
        $renderer = new OptionsMenuRenderer();
        $group_menu->render($renderer, 'sitemap');
        return $renderer->toArray();
    }

    /**
     * Sets default values.
     *
     * @param array $defaults Default values for this form's parameters.
     */
    public function setDefaults($defaults = array ())
    {
        $group = $this->group;
        $defaults[self::PROPERTY_LOCATION] = $group->get_parent();
        parent::setDefaults($defaults);
    }
}
