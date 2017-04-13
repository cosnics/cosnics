<?php

namespace Chamilo\Libraries\Format\Structure;

/**
 * Renders a bootstrap panel
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PanelRenderer
{
    const MODE_DEFAULT = 'default';
    const MODE_PRIMARY = 'primary';
    const MODE_SUCCESS = 'success';
    const MODE_INFO = 'info';
    const MODE_WARNING = 'warning';
    const MODE_DANGER = 'danger';

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
        $html = array();

        $html[] = $this->renderPanelHeader($title, $mode);
        $html[] = '<div class="panel-body">';
        $html[] = $content;
        $html[] = '</div>';
        $html[] = $this->renderPanelFooter();

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
    public function renderTablePanel($title, $tableValues = array(), $mode = self::MODE_DEFAULT)
    {
        $html = array();
        $html[] = $this->renderPanelHeader($title, $mode);

        $html[] = '<table class="table table-bordered">';

        foreach ($tableValues as $key => $value)
        {
            $html[] = '<tr>';
            $html[] = '<td width="25%"><strong>' . $key . '</strong></td>';
            $html[] = '<td>' . $value . '</td>';
            $html[] = '</tr>';
        }

        $html[] = '</table>';
        $html[] = $this->renderPanelFooter();

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

        $html = array();

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
     * Renders the footer for the panel
     *
     * @return string
     */
    protected function renderPanelFooter()
    {
        $html = array();

        $html[] = '</div>';

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
            self::MODE_DEFAULT, self::MODE_PRIMARY, self::MODE_SUCCESS, self::MODE_INFO, self::MODE_WARNING,
            self::MODE_DANGER
        );
    }

    /**
     * Validates the mode of the progress bar
     *
     * @param string $mode
     */
    protected function validateMode($mode = self::MODE_DEFAULT)
    {
        if (!in_array($mode, $this->getAllowedModes()))
        {
            throw new \InvalidArgumentException(
                sprintf(
                    'The given mode must be a valid string and must be one of (%s)',
                    implode(', ', $this->getAllowedModes())
                )
            );
        }
    }
}