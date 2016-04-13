<?php
namespace Chamilo\Libraries\Format\Menu\DynamicContentMenu;

/**
 * Extension on the dynamic content menu item to display this menu item in a form
 * 
 * @package \libraries
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FormDynamicContentMenuItem extends DynamicContentMenuItem
{

    /**
     * **************************************************************************************************************
     * Render Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Adds the menu to the form
     * 
     * @param FormValidator $form
     */
    public function add_to_form($form)
    {
        $form->addElement('html', $this->render_content_header());
        call_user_func(array($form, $this->get_content_function()), $this);
        $form->addElement('html', $this->render_content_footer());
    }
}
