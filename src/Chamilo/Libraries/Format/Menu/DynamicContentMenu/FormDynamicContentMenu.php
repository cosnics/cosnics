<?php
namespace Chamilo\Libraries\Format\Menu\DynamicContentMenu;

/**
 * Extension on the dynamic content menu to display this menu in a form
 *
 * @package Chamilo\Libraries\Format\Menu\DynamicContentMenu
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FormDynamicContentMenu extends DynamicContentMenu
{

    /**
     * Adds the menu to the form
     *
     * @param \Chamilo\Libraries\Format\Form\FormValidator $form
     */
    public function add_to_form($form)
    {
        $form->addElement('html', $this->render_header());

        foreach ($this->get_menu_items() as $menu_item)
        {
            $menu_item->add_to_form($form);
        }

        $form->addElement('html', $this->render_footer());
    }
}
