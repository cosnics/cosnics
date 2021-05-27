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
    const MODE_DANGER = 'danger';
    const MODE_DEFAULT = 'default';
    const MODE_INFO = 'info';
    const MODE_PRIMARY = 'primary';
    const MODE_SUCCESS = 'success';
    const MODE_WARNING = 'warning';

    /**
     * Renders a bootstrap panel
     *
     * @param string $title
     * @param string $content
     * @param string $mode
     *
     * @return string
     */
    public function render($title, $content, $mode = self::MODE_DEFAULT)
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
     * Returns a list of the allowed modes
     *
     * @return string[]
     */
    protected function getAllowedModes()
    {
        return array(
            self::MODE_DEFAULT,
            self::MODE_PRIMARY,
            self::MODE_SUCCESS,
            self::MODE_INFO,
            self::MODE_WARNING,
            self::MODE_DANGER
        );
    }

    /**
     * Renders the footer for the panel
     *
     * @return string
     */
    protected function renderPanelFooter()
    {
        $html = [];

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Renders the header for the panel
     *
     * @param string $title
     * @param string $mode
     *
     * @return string
     */
    protected function renderPanelHeader($title = null, $mode = self::MODE_DEFAULT)
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
     *
     * @param string $title
     * @param string[] $tableValues
     * @param string $mode
     *
     * @return string
     */
    public function renderTablePanel($title, $tableValues = [], $mode = self::MODE_DEFAULT)
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
     * Validates the mode of the panel renderer
     *
     * @param string $mode
     *
     * @throws \InvalidArgumentException
     */
    protected function validateMode($mode = self::MODE_DEFAULT)
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