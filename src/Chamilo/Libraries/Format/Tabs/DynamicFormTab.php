<?php
namespace Chamilo\Libraries\Format\Tabs;

class DynamicFormTab extends DynamicTab
{

    private $method;

    /**
     * The parameters needed to call the method
     *
     * @var array
     */
    private $parameters;

    private $form;

    /**
     *
     * @param integer $id
     * @param string $name
     * @param string $image
     * @param string $method
     * @param array $parameters
     */
    public function __construct($id, $name, $image, $method, $parameters = array())
    {
        parent :: __construct($id, $name, $image);
        $this->method = $method;
        $this->parameters = $parameters;
    }

    /**
     *
     * @return the $method
     */
    public function get_method()
    {
        return $this->method;
    }

    /**
     *
     * @param $method the $method to set
     */
    public function set_method($method)
    {
        $this->method = $method;
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

    /**
     *
     * @return string
     */
    public function body($isOnlyTab = false)
    {
        if (! $isOnlyTab)
        {
            $this->get_form()->addElement('html', $this->body_header());
        }

        $method = $this->get_method();

        if (! is_array($method))
        {
            $method = array($this->get_form(), $method);
        }

        call_user_func_array($method, $this->parameters);

        if (! $isOnlyTab)
        {
            $this->get_form()->addElement('html', $this->body_footer());
        }
    }

    public function get_link()
    {
        return "#" . $this->get_id();
    }
}
