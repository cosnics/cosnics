<?php
namespace Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element;

use HTML_QuickForm;
use HTML_QuickForm_file;
use QuickformException;
use ReflectionClass;

/**
 * @package Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class HTML_QuickForm_stylefile extends HTML_QuickForm_file
{
    protected ?string $instructions;

    public function __construct(
        ?string $elementName = null, ?string $elementLabel = null, array|string|null $attributes = null,
        ?string $instructions = null
    )
    {
        parent::__construct($elementName, $elementLabel, $attributes);
        $this->instructions = $instructions;

        $defaultAttributes = [];
        $defaultAttributes[] = $this->getAttribute('class');
        $defaultAttributes[] = 'form-control';

        $this->setAttribute('class', implode(' ', $defaultAttributes));
    }

    public function onQuickFormEvent(string $event, mixed $arg, ?HTML_QuickForm $caller = null): bool
    {
        switch ($event) {
            case 'updateValue':
                if ($caller->getAttribute('method') == 'get') {
                    throw new QuickformException('Cannot add a file upload field to a GET method form');
                }

                $values = [];
                $this->_value = $this->_findValue($values);
                $caller->updateAttributes(['enctype' => 'multipart/form-data']);
                $caller->setMaxFileSize();
                break;
            case 'addElement':
                $this->onQuickFormEvent('createElement', $arg, $caller);

                return $this->onQuickFormEvent('updateValue', null, $caller);
            case 'createElement':
                $class = new ReflectionClass($this);
                $parameters = $class->getConstructor()->getParameters();

                foreach ($parameters as $key => $parameter) {
                    $arg[$key] = is_null($arg[$key]) ?
                        ($parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null) : $arg[$key];
                }

                static::__construct($arg[0], $arg[1], $arg[2], $arg[3]);
                break;
        }

        return true;
    }

    public function toHtml(): string
    {
        $html = [];

        $html[] = parent::toHtml();

        if ($this->instructions) {
            $html[] = '<div class="form-text">' . $this->instructions . '</div>';
        }

        return implode(PHP_EOL, $html);
    }
}
