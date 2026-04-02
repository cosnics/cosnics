<?php
namespace Chamilo\Libraries\UserInterface\Layout\Service;

use Chamilo\Libraries\UserInterface\Layout\Architecture\Enum\PanelModeEnum;

/**
 * @package Chamilo\Libraries\UserInterface\Layout\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CardRenderer
{
    public function render(
        string $content, ?string $header = null, ?string $title = null, PanelModeEnum $mode = PanelModeEnum::DEFAULT
    ): string
    {
        $html = [];

        $html[] = '<div class="card border-' . $mode->value . ' mb-2">';

        if ($header) {
            $html[] = '<div class="card-header text-bg-' . $mode->value . ' border-' . $mode->value . '">' . $title .
                '</div>';
        }

        $html[] = '<div class="card-body">';

        if ($title) {
            $html[] = '<h5 class="card-title">' . $title . '</h5>';
        }

        $html[] = '<p class="card-text">' . $content . '</p>';
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function renderTablePanel(
        array $tableValues = [], ?string $header = null, ?string $title = null,
        PanelModeEnum $mode = PanelModeEnum::DEFAULT
    ): string
    {
        $html = [];

        $html[] = '<div class="card border-' . $mode->value . ' mb-2">';

        if ($header) {
            $html[] = '<div class="card-header bg-' . $mode->value . ' border-' . $mode->value . '">';
            $html[] = $header;
            $html[] = '</div>';
        }

        $html[] = '<div class="card-body">';

        if ($title) {
            $html[] = '<h5 class="card-title">' . $title . '</h5>';
        }

        $html[] = '<div class="card-text">';

        foreach ($tableValues as $key => $value) {
            $html[] = '<div class="form-floating mb-2">';
            $html[] = '<div class="form-control">' . $value . '</div>';
            $html[] = '<label><strong>' . $key . '</strong></label>';
            $html[] = '</div>';
        }

        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}