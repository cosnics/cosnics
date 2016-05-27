<?php
namespace Chamilo\Libraries\Format\Tabs;

class DynamicFormTabsRenderer extends DynamicTabsRenderer
{

    private $form;

    public function __construct($name, $form)
    {
        parent :: __construct($name);
        $this->form = $form;
    }

    /**
     *
     * @return the $form
     */
    public function get_form()
    {
        return $this->form;
    }

    /**
     *
     * @param $form the $form to set
     */
    public function set_form($form)
    {
        $this->form = $form;
    }

    public function render()
    {
        $tabCount = count($this->get_tabs());

        if ($tabCount > 1)
        {
            $this->form->addElement('html', $this->header());
        }

        foreach ($this->get_tabs() as $key => $tab)
        {
            $tab->set_form($this->form);
            $tab->body($tabCount == 1);
        }

        if ($tabCount > 1)
        {
            $this->form->addElement('html', $this->footer());
        }
    }
}
