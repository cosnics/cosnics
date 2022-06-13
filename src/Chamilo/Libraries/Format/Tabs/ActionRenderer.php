<?php
namespace Chamilo\Libraries\Format\Tabs;

/**
 *
 * @package Chamilo\Libraries\Format\Tabs
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActionRenderer
{
    public function render(Action $action): string
    {
        $html = [];

        if ($action->hasConfirmationMessage())
        {
            $onclick = 'onclick = "return confirm(\'' . $action->getConfirmationMessage() . '\')"';
        }
        else
        {
            $onclick = '';
        }

        $html[] = '<div class="list-group-item vertical-action">';

        $html[] = '<div class="pull-left icon">';
        $html[] = '<a href="' . $action->getUrl() . '" ' . $onclick . '>';

        $html[] = $action->getInlineGlyph()->render();

        $html[] = '</a>';
        $html[] = '</div>';

        $html[] = '<div class="pull-left">';

        if ($action->getTitle())
        {
            $html[] = '<h5 class="list-group-item-heading"><a href="' . $action->getUrl() . '" ' . $onclick . '>' .
                $action->getTitle() . '</a></h5>';
        }

        $html[] = '<p class="list-group-item-text">' . $action->getContent() . '</p>';
        $html[] = '</div>';

        $html[] = '<div class="clearfix"></div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}