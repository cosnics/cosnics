<?php
namespace Chamilo\Libraries\Format\Tabs\Form;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;
use Chamilo\Libraries\Format\Tabs\Tab;

/**
 *
 * @package Chamilo\Libraries\Format\Tabs
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FormTab extends Tab
{

    private FormValidator $form;

    /**
     * @var string|string[] $method
     */
    private string $method;

    /**
     * @var string[]
     */
    private array $parameters;

    /**
     * @param string|string[] $method
     */
    public function __construct(
        string $identifier, string $label, ?InlineGlyph $inlineGlyph, $method, array $parameters = []
    )
    {
        parent::__construct($identifier, $label, $inlineGlyph);
        $this->method = $method;
        $this->parameters = $parameters;
    }

    public function body(bool $isOnlyTab = false): string
    {
        if (!$isOnlyTab)
        {
            $this->getForm()->addElement('html', $this->bodyHeader());
        }

        $method = $this->getMethod();

        if (!is_array($method))
        {
            $method = array($this->getForm(), $method);
        }

        call_user_func_array($method, $this->parameters);

        if (!$isOnlyTab)
        {
            $this->getForm()->addElement('html', $this->bodyFooter());
        }

        return '';
    }

    public function getForm(): FormValidator
    {
        return $this->form;
    }

    public function setForm(FormValidator $form)
    {
        $this->form = $form;
    }

    public function getLink(): string
    {
        return '#' . $this->getIdentifier();
    }

    /**
     * @return string|string[]
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string|string[] $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }
}
