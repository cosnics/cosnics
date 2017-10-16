<?php
namespace Chamilo\Libraries\Format\Tabs;

/**
 *
 * @package Chamilo\Libraries\Format\Tabs
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DynamicFormTab extends DynamicTab
{

    /**
     *
     * @var string
     */
    private $method;

    /**
     * The parameters needed to call the method
     *
     * @var string[]
     */
    private $parameters;

    /**
     *
     * @var unknown
     */
    private $form;

    /**
     *
     * @param integer $id
     * @param string $name
     * @param string|\Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph $image
     * @param string $method
     * @param string[] $parameters
     */
    public function __construct($id, $name, $image, $method, $parameters = array())
    {
        parent::__construct($id, $name, $image);
        $this->method = $method;
        $this->parameters = $parameters;
    }

    /**
     *
     * @return string
     */
    public function get_method()
    {
        return $this->method;
    }

    /**
     *
     * @param string $method
     */
    public function set_method($method)
    {
        $this->method = $method;
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
     * @see \Chamilo\Libraries\Format\Tabs\DynamicTab::body()
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

    /**
     *
     * @see \Chamilo\Libraries\Format\Tabs\DynamicTab::get_link()
     */
    public function get_link()
    {
        return "#" . $this->get_id();
    }
}
