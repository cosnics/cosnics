<?php

namespace Chamilo\Libraries\Format\Structure;

/**
 * Renders a progressbar
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ProgressBarRenderer
{
    const MODE_DEFAULT = 'default';
    const MODE_SUCCESS = 'success';
    const MODE_INFO = 'info';
    const MODE_WARNING = 'warning';
    const MODE_DANGER = 'danger';

    /**
     * Renders a progressbar for a given percentage
     *
     * @param int $progress - progress between 0 and 100
     * @param string $mode
     *
     * @return string
     */
    public function render($progress, $mode = self::MODE_DEFAULT, $maxWidth = 150)
    {
        $this->validateProgress($progress);
        $this->validateMode($mode);

        $maxWidth = is_integer($maxWidth) && $maxWidth > 0 ? $maxWidth . 'px' : '100%';

        $contextualClass = $mode == self::MODE_DEFAULT ? '' : 'progress-bar-' . $mode;

        $html = array();

        $html[] = '<div class="progress" style="margin-bottom: 0; max-width: ' . $maxWidth . ';">';
        $html[] = '<div class="progress-bar ' . $contextualClass . '" role="progressbar" aria-valuenow="' . $progress;
        $html[] = '" aria-valuemin="0" aria-valuemax="100" style="min-width: 2em; width: ' . $progress . '%;">';
        $html[] = $progress . '%';
        $html[] = '</div>';
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
        return array(self::MODE_DEFAULT, self::MODE_SUCCESS, self::MODE_INFO, self::MODE_WARNING, self::MODE_DANGER);
    }

    /**
     * Validates the progress parameter
     *
     * @param int $progress
     */
    protected function validateProgress($progress)
    {
        if (!is_int($progress) || $progress < 0 || $progress > 100)
        {
            throw new \InvalidArgumentException(
                'The given progress must be a valid integer and must be between 0 and 100'
            );
        }
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