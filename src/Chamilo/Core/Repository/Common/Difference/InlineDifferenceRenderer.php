<?php
namespace Chamilo\Core\Repository\Common\Difference;

use Chamilo\Libraries\Translation\Translation;
use Diff_Renderer_Html_Array;

/**
 * @package Chamilo\Core\Repository\Common\Difference
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class InlineDifferenceRenderer extends Diff_Renderer_Html_Array
{
    /**
     * Render a and return diff with changes between the two sequences
     * displayed inline (under each other)
     *
     * @return string The generated inline diff.
     */
    public function render()
    {
        $changes = parent::render();

        $translator = Translation::getInstance();

        $html = '';

        if (empty($changes))
        {
            return $html;
        }

        $html .= '<table class="table table-difference">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th class="col-sm-1">' . $translator->getTranslation('Old', [], 'Chamilo\Core\Repository') .
            '</th>';
        $html .= '<th class="col-sm-1">' . $translator->getTranslation('New', [], 'Chamilo\Core\Repository') .
            '</th>';
        $html .= '<th class="col-sm-10">' .
            $translator->getTranslation('Differences', [], 'Chamilo\Core\Repository') . '</th>';
        $html .= '</tr>';
        $html .= '</thead>';

        foreach ($changes as $i => $blocks)
        {
            // If this is a separate block, we're condensing code so output ...,
            // indicating a significant portion of the code has been collapsed as
            // it is the same
            if ($i > 0)
            {
                $html .= '<tbody class="Skipped">';
                $html .= '<tr>';
                $html .= '<th class="col-sm-1">&hellip;</th>';
                $html .= '<th class="col-sm-1">&hellip;</th>';
                $html .= '<td class="col-sm-10">&nbsp;</td>';
                $html .= '</tr>';
                $html .= '</tbody>';
            }

            foreach ($blocks as $change)
            {
                $html .= '<tbody class="Change' . ucfirst($change['tag']) . '">';
                // Equal changes should be shown on both sides of the diff
                if ($change['tag'] == 'equal')
                {
                    foreach ($change['base']['lines'] as $no => $line)
                    {
                        $fromLine = $change['base']['offset'] + $no + 1;
                        $toLine = $change['changed']['offset'] + $no + 1;
                        $html .= '<tr>';
                        $html .= '<th class="col-sm-1 Left">' . $fromLine . '</th>';
                        $html .= '<th class="col-sm-1 Left">' . $toLine . '</th>';
                        $html .= '<td class="col-sm-10 Left">' . $line . '</td>';
                        $html .= '</tr>';
                    }
                }
                // Added lines only on the right side
                else
                {
                    if ($change['tag'] == 'insert')
                    {
                        foreach ($change['changed']['lines'] as $no => $line)
                        {
                            $toLine = $change['changed']['offset'] + $no + 1;
                            $html .= '<tr>';
                            $html .= '<th class="col-sm-1 Right">&nbsp;</th>';
                            $html .= '<th class="col-sm-1 Right">' . $toLine . '</th>';
                            $html .= '<td class="col-sm-10 Right"><ins>' . $line . '</ins>&nbsp;</td>';
                            $html .= '</tr>';
                        }
                    }
                    // Show deleted lines only on the left side
                    else
                    {
                        if ($change['tag'] == 'delete')
                        {
                            foreach ($change['base']['lines'] as $no => $line)
                            {
                                $fromLine = $change['base']['offset'] + $no + 1;
                                $html .= '<tr>';
                                $html .= '<th class="col-sm-1 Left">' . $fromLine . '</th>';
                                $html .= '<th class="col-sm-1 Left">&nbsp;</th>';
                                $html .= '<td class="col-sm-10 Left"><del>' . $line . '</del>&nbsp;</td>';
                                $html .= '</tr>';
                            }
                        }
                        // Show modified lines on both sides
                        else
                        {
                            if ($change['tag'] == 'replace')
                            {
                                foreach ($change['base']['lines'] as $no => $line)
                                {
                                    $fromLine = $change['base']['offset'] + $no + 1;
                                    $html .= '<tr>';
                                    $html .= '<th class="col-sm-1 Left">' . $fromLine . '</th>';
                                    $html .= '<th class="col-sm-1 Left">&nbsp;</th>';
                                    $html .= '<td class="col-sm-10 Left"><span>' . $line . '</span></td>';
                                    $html .= '</tr>';
                                }

                                foreach ($change['changed']['lines'] as $no => $line)
                                {
                                    $toLine = $change['changed']['offset'] + $no + 1;
                                    $html .= '<tr>';
                                    $html .= '<th class="col-sm-1 Right">' . $toLine . '</th>';
                                    $html .= '<th class="col-sm-1 Right">&nbsp;</th>';
                                    $html .= '<td class="col-sm-10 Right"><span>' . $line . '</span></td>';
                                    $html .= '</tr>';
                                }
                            }
                        }
                    }
                }
                $html .= '</tbody>';
            }
        }
        $html .= '</table>';

        return $html;
    }
}