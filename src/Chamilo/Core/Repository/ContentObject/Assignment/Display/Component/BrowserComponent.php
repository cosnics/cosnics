<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Table\PropertiesTable;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class BrowserComponent extends Manager implements TableSupport
{

    public function run()
    {
        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->renderDetails();
        $html[] = $this->renderReporting();
        $html[] = $this->renderEntryTable();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    private function renderDetails()
    {
        $display = ContentObjectRenditionImplementation :: factory(
            $this->get_root_content_object(),
            ContentObjectRendition :: FORMAT_HTML,
            ContentObjectRendition :: VIEW_FULL,
            $this);

        return $display->render();
    }

    /**
     *
     * @return string
     */
    private function renderReporting()
    {
        $html = array();

        $html[] = '<div class="content_object" style="background-image: url(' .
             Theme :: getInstance()->getImagePath('Chamilo\Core\Reporting', 'Logo/16') . ');">';
        $html[] = '<div class="title">' . Translation :: get('Reporting') . '</div>';

        $entryCount = $this->getDataProvider()->countEntriesForEntityTypeAndId(
            $this->getEntityType(),
            $this->getEntityIdentifier());
        $feedbackCount = $this->getDataProvider()->countDistinctFeedbackForEntityTypeAndId(
            $this->getEntityType(),
            $this->getEntityIdentifier());
        $scoreCount = $this->getDataProvider()->countDistinctScoreForEntityTypeAndId(
            $this->getEntityType(),
            $this->getEntityIdentifier());
        $averageScore = $this->getDataProvider()->getAverageScoreForEntityTypeAndId(
            $this->getEntityType(),
            $this->getEntityIdentifier());

        $averageScoreValue = isset($averageScore[AssignmentDataProvider :: AVERAGE_SCORE]) ? $averageScore[AssignmentDataProvider :: AVERAGE_SCORE] .
             ' %' : '-';

        $properties = array();
        $properties[Translation :: get('EntriesWithScore')] = $scoreCount . '/' . $entryCount;
        $properties[Translation :: get('EntriesWithFeedback')] = $feedbackCount . '/' . $entryCount;
        $properties[Translation :: get('AverageScore')] = $averageScoreValue;

        $table = new PropertiesTable($properties);

        $html[] = $table->toHtml();

        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    private function renderEntryTable()
    {
        return $this->getDataProvider()->getEntryTableForEntityTypeAndId(
            $this,
            $this->getEntityType(),
            $this->getEntityIdentifier())->as_html();
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\Interfaces\TableSupport::get_table_condition()
     */
    public function get_table_condition($tableClassName)
    {
        // TODO Auto-generated method stub
    }
}
