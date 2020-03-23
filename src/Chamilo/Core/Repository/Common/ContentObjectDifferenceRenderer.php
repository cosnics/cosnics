<?php
namespace Chamilo\Core\Repository\Common;

use Chamilo\Core\Repository\Common\Difference\InlineDifferenceRenderer;
use Chamilo\Core\Repository\Common\Difference\SideBySide;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Repository\Common
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContentObjectDifferenceRenderer
{
    /**
     * @param \Chamilo\Core\Repository\Common\ContentObjectDifference $contentObjectDifference
     *
     * @return string
     */
    public function render(ContentObjectDifference $contentObjectDifference)
    {
        $html = array();
        $renderer = new InlineDifferenceRenderer();
        //        $renderer = new \Diff_Renderer_Html_Inline();

        $translator = Translation::getInstance();

        $html[] = '<div class="row">';
        $html[] = '<div class="col-md-6">';
        $html[] = '<h4>';
        $html[] = $contentObjectDifference->getContentObjectVersion()->get_title();
        $html[] = '<div class="small">' . $translator->getTranslation('OldVersion') . '</div>';
        $html[] = '</h4>';
        $html[] = '</div>';
        $html[] = '<div class="col-md-6">';
        $html[] = '<h4>';
        $html[] = $contentObjectDifference->getContentObject()->get_title();
        $html[] = '<div class="small">' . $translator->getTranslation('NewVersion') . '</div>';
        $html[] = '</h4>';
        $html[] = '</div>';
        $html[] = '</div>';

        foreach ($contentObjectDifference->compare() as $propertyName => $difference)
        {
            $renderedDifference = $difference->Render($renderer);

            if ($renderedDifference)
            {
                $propertyVariable =
                    StringUtilities::getInstance()->createString($propertyName)->upperCamelize()->__toString();
                $propertyLabel = Translation::getInstance()->getTranslation(
                    $propertyVariable, array(), $contentObjectDifference->getContentObject()->package()
                );

                $html[] = '<div class="panel panel-default">';
                $html[] = '<div class="panel-heading">' . $propertyLabel . '</div>';
                $html[] = $renderedDifference;
                $html[] = '</div>';
            }
        }

        return implode(PHP_EOL, $html);
    }
}
