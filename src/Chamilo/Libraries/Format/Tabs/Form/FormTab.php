<?php
namespace Chamilo\Libraries\Format\Tabs\Form;

use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;
use Chamilo\Libraries\Format\Tabs\GenericTab;

/**
 * @package Chamilo\Libraries\Format\Tabs
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FormTab extends GenericTab
{

    /**
     * @var string|string[] $method
     */
    private string|array $method;

    /**
     * @var string[]
     */
    private array $parameters;

    /**
     * @param string|string[] $method
     */
    public function __construct(
        string $identifier, string $label, ?InlineGlyph $inlineGlyph, $method, array $parameters = [],
        int $display = self::DISPLAY_ICON_AND_TITLE
    )
    {
        parent::__construct($identifier, $label, $inlineGlyph, $display);
        $this->method = $method;
        $this->parameters = $parameters;
    }

    /**
     * @return string|string[]
     */
    public function getMethod(): array|string
    {
        return $this->method;
    }

    /**
     * @param string|string[] $method
     */
    public function setMethod(array|string $method): static
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param string[] $parameters
     */
    public function setParameters(array $parameters): static
    {
        $this->parameters = $parameters;

        return $this;
    }
}
