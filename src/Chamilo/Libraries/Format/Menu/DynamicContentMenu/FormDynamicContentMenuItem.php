<?php
namespace Chamilo\Libraries\Format\Menu\DynamicContentMenu;

/**
 * Extension on the dynamic content menu item to display this menu item in a form
 *
 * @package Chamilo\Libraries\Format\Menu\DynamicContentMenu
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FormDynamicContentMenuItem extends DynamicContentMenuItem
{

    /**
     * Adds the menu to the form
     *
     * @param \Chamilo\Libraries\Format\Form\FormValidator $form
     */
    public function add_to_form($form)
    {
        $form->addElement('html', $this->render_content_header());
        call_user_func(array($form, $this->get_content_function()), $this);
    }
}
