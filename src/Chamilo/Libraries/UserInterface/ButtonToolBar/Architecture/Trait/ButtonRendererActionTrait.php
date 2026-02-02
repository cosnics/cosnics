<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait;

use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonActionInterface;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait ButtonRendererActionTrait
{
    public function renderAction(ButtonActionInterface $button): string
    {
        $html = [];

        if ($button->getAction()) {
            $html[] = 'href="' . htmlentities($button->getAction()) . '"';

            if ($button->getTarget()) {
                $html[] = 'target="' . $button->getTarget() . '"';
            }

            if ($button->needsConfirmation()) {
                $html[] = 'onclick="return confirm(\'' . addslashes(htmlentities($button->getConfirmationMessage())) .
                    '\');"';
            }
        }

        return implode(' ', $html);
    }
}