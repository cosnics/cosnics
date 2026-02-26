<?php
namespace Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element;

use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;

/**
 * @package Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class HTML_QuickForm_button extends HTML_QuickForm_button_abstract
{
    public function __construct(
        ?string $elementName = null, ?string $elementLabel = null, null|array|string $attributes = null,
        ?string $value = null, ?InlineGlyph $glyph = null
    )
    {
        parent::__construct($elementName, $elementLabel, $attributes, $value, $glyph);

        $defaultAttributes = [];
        $defaultAttributes[] = $this->getAttribute('class');
        $defaultAttributes[] = 'btn-outline-secondary';

        $this->setAttribute('class', implode(' ', $defaultAttributes));
    }
}
