<?php
namespace Chamilo\Libraries\Format\Structure;

use InvalidArgumentException;

/**
 * Renders a bootstrap panel
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PanelRenderer
{
    public const MODE_DANGER = 'danger';
    public const MODE_DEFAULT = 'default';
    public const MODE_INFO = 'info';
    public const MODE_PRIMARY = 'primary';
    public const MODE_SUCCESS = 'success';
    public const MODE_WARNING = 'warning';

    public function render(string $content, ?string $title = null, string $mode = self::MODE_DEFAULT): string
    {
        $html = [];

        $html[] = $this->renderPanelHeader($title, $mode);
        $html[] = '<div class="panel-body">';
        $html[] = $content;
        $html[] = '</div>';
        $html[] = $this->renderPanelFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string[]
     */
    protected function getAllowedModes(): array
    {
        return [
            self::MODE_DEFAULT,
            self::MODE_PRIMARY,
            self::MODE_SUCCESS,
            self::MODE_INFO,
            self::MODE_WARNING,
            self::MODE_DANGER
        ];
    }

    protected function renderPanelFooter(): string
    {
        $html = [];

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    protected function renderPanelHeader(?string $title = null, string $mode = self::MODE_DEFAULT): string
    {
        $this->validateMode($mode);

        $contextualClass = 'panel-' . $mode;

        $html = [];

        $html[] = '<div class="panel ' . $contextualClass . '">';

        if (!is_null($title))
        {
            $html[] = '<div class="panel-heading">';
            $html[] = '<h5 class="panel-title">' . $title . '</h5>';
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * Renders a panel with a table based on key => value data
     */
    public function renderTablePanel(array $tableValues = [], ?string $title = null, string $mode = self::MODE_DEFAULT
    ): string
    {
        $html = [];
        $html[] = $this->renderPanelHeader($title, $mode);

        $html[] = '<table class="table table-bordered">';

        foreach ($tableValues as $key => $value)
        {
            $html[] = '<tr>';
            $html[] = '<td class="cell-stat-2x"><strong>' . $key . '</strong></td>';
            $html[] = '<td>' . $value . '</td>';
            $html[] = '</tr>';
        }

        $html[] = '</table>';
        $html[] = $this->renderPanelFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function validateMode(string $mode = self::MODE_DEFAULT)
    {
        if (!in_array($mode, $this->getAllowedModes()))
        {
            throw new InvalidArgumentException(
                sprintf(
                    'The given mode must be a valid string and must be one of (%s)',
                    implode(', ', $this->getAllowedModes())
                )
            );
        }
    }
}