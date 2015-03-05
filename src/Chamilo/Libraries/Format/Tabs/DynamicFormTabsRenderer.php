<?php
namespace Chamilo\Libraries\Format\Tabs;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;

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
        $this->form->addElement('html', $this->header());

        foreach ($this->get_tabs() as $key => $tab)
        {
            $tab->set_form($this->form);
            $tab->body();
        }

        $this->form->addElement('html', $this->footer());
    }

    public function footer()
    {
        $html = array();
        $html[] = '<script type="text/javascript">';
        $html[] = ' var element = "' . $this->get_name() . '"';
        $html[] = '</script>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->getJavascriptPath('Chamilo\Configuration', true) . 'DynamicFormTabs.js');
        $html[] = parent :: footer();
        return implode(PHP_EOL, $html);
    }
}
