<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait;

use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonAttributesInterface;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait ButtonRendererAttributesTrait
{
    public function renderAttributes(ButtonAttributesInterface $button): string
    {
        $html = [];

        foreach ($button->getAttributes() as $name => $values) {
            $html[] = $name . '="' . htmlentities(implode(' ', $values)) . '"';
        }

        return implode(' ', $html);
    }
}