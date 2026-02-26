<?php
namespace Chamilo\Libraries\UserInterface\Tab\Service;

use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\Action;

/**
 * @package Chamilo\Libraries\UserInterface\Tab\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActionRenderer
{
    public function render(Action $action): string
    {
        $html = [];

        if ($action->hasConfirmationMessage()) {
            $onclick = 'onclick = "return confirm(\'' . $action->getConfirmationMessage() . '\')"';
        }
        else {
            $onclick = '';
        }

        $html[] = '<li class="list-group-item vertical-action clearfix">';

        $html[] = '<div class="float-start my-2 me-3">';
        $html[] = '<a href="' . $action->getUrl() . '" ' . $onclick . '>';

        $html[] = $action->getInlineGlyph()->render();

        $html[] = '</a>';
        $html[] = '</div>';

        $html[] = '<div class="float-start">';

        if ($action->getTitle()) {
            $html[] = '<h5 class="list-group-item-heading"><a href="' . $action->getUrl() . '" ' . $onclick . '>' .
                $action->getTitle() . '</a></h5>';
        }

        $html[] = '<p>' . $action->getContent() . '</p>';
        $html[] = '</div>';

        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }
}