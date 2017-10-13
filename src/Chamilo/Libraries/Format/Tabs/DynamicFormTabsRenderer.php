<?php
namespace Chamilo\Libraries\Format\Tabs;

/**
 *
 * @package Chamilo\Libraries\Format\Tabs
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DynamicFormTabsRenderer extends DynamicTabsRenderer
{

    /**
     *
     * @var \Chamilo\Libraries\Format\Form\FormValidator
     */
    private $form;

    /**
     *
     * @param string $name
     * @param \Chamilo\Libraries\Format\Form\FormValidator $form
     */
    public function __construct($name, $form)
    {
        parent::__construct($name);
        $this->form = $form;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Form\FormValidator
     */
    public function get_form()
    {
        return $this->form;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Form\FormValidator $form
     */
    public function set_form($form)
    {
        $this->form = $form;
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer::render()
     */
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
