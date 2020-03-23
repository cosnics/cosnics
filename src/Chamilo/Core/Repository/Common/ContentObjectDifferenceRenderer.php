<?php
namespace Chamilo\Core\Repository\Common;

use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Diff;
use Diff_Renderer_Html_SideBySide;

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
        $renderer = new Diff_Renderer_Html_SideBySide();

        $html[] = '<div class="row">';
        $html[] = '<div class="col-md-6">';
        $html[] = '<h4>';
        $html[] = $contentObjectDifference->getContentObjectVersion()->get_title();
        $html[] = '</h4>';
        $html[] = '</div>';
        $html[] = '<div class="col-md-6">';
        $html[] = '<h4>';
        $html[] = $contentObjectDifference->getContentObject()->get_title();
        $html[] = '</h4>';
        $html[] = '</div>';
        $html[] = '</div>';

        foreach ($contentObjectDifference->compare() as $difference)
        {
            $html[] = $difference->Render($renderer);
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Core\Repository\Common\ContentObjectDifference $contentObjectDifference
     *
     * @return string
     */
    public function renderContent(ContentObjectDifference $contentObjectDifference)
    {
        $html = array();

        $difference = new Diff(
            $contentObjectDifference->getOldVersionStrings(), $contentObjectDifference->getNewVersionStrings()
        );
        $renderer = new Diff_Renderer_Html_SideBySide();

        $renderedDifference = $difference->Render($renderer);

        $translator = Translation::getInstance();

        $renderedDifference = str_replace(
            '<th colspan="2">Old Version</th>',
            '<th colspan="2">' . $translator->getTranslation('OldVersion') . '</th>', $renderedDifference
        );

        $renderedDifference = str_replace(
            '<th colspan="2">New Version</th>',
            '<th colspan="2">' . $translator->getTranslation('NewVersion') . '</th>', $renderedDifference
        );

        $html = array();

        $html[] = '<div class="panel panel-default">';
        $html[] = '<div class="panel-heading">Panel heading</div>';
        $html[] = $renderedDifference;
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Core\Repository\Common\ContentObjectDifference $contentObjectDifference
     *
     * @return string
     */
    public function renderProperties(ContentObjectDifference $contentObjectDifference)
    {
        $newVersion = $contentObjectDifference->get_object();
        $oldVersion = $contentObjectDifference->get_version();

        $html = array();

        $html[] = '<table class="table table-bordered table-striped comparer-header-table">';

        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th></th>';
        $html[] = '<th>' . Translation::getInstance()->getTranslation('OldVersion') . '</th>';
        $html[] = '<th>' . Translation::getInstance()->getTranslation('NewVersion') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';

        $html[] = '<tbody>';

        $html[] = '<tr>';
        $html[] = '<th class="comparer-header-title">' . Translation::getInstance()->getTranslation('Title') . '</th>';
        $html[] = '<td>' . $oldVersion->get_title() . '</td>';
        $html[] = '<td>' . $newVersion->get_title() . '</td>';
        $html[] = '</tr>';

        $html[] = '<tr>';
        $html[] = '<th class="comparer-header-title">' . Translation::getInstance()->getTranslation('Date') . '</th>';

        $html[] = '<td>' . DatetimeUtilities::format_locale_date(
                Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES),
                $oldVersion->get_modification_date()
            ) . '</td>';
        $html[] = '<td>' . DatetimeUtilities::format_locale_date(
                Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES),
                $newVersion->get_modification_date()
            ) . '</td>';
        $html[] = '</tr>';

        if ($oldVersion->get_comment() || $newVersion->get_comment())
        {
            $html[] = '<tr>';
            $html[] =
                '<th class="comparer-header-title">' . Translation::getInstance()->getTranslation('VersionComment') .
                '</th>';
            $html[] = '<td>' . $oldVersion->get_comment() . '</td>';
            $html[] = '<td>' . $newVersion->get_comment() . '</td>';
            $html[] = '</tr>';
        }

        $html[] = '</tbody>';

        $html[] = '</table>';

        return implode(PHP_EOL, $html);
    }
}
