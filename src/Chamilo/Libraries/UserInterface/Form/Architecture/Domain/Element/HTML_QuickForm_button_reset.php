<?php
namespace Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element;

use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;

/**
 * @package Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class HTML_QuickForm_button_reset extends HTML_QuickForm_button_abstract
{
    public function __construct(
        ?string $elementName = null, ?string $elementLabel = null, null|array|string $attributes = null,
        ?string $value = null, ?InlineGlyph $glyph = null
    )
    {
        // Quickform forces all arguments to "null", so the defaults in the constructor are not triggered
        if (!isset($glyph)) {
            $glyph = new FontAwesomeGlyph('trash-alt');
        }

        parent::__construct($elementName, $elementLabel, $attributes, $value, $glyph);

        $this->setType('reset');

        $defaultAttributes = [];
        $defaultAttributes[] = $this->getAttribute('class');
        $defaultAttributes[] = 'btn-secondary';

        $this->setAttribute('class', implode(' ', $defaultAttributes));
    }
}
