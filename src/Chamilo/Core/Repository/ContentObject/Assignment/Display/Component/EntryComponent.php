<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Table\PropertiesTable;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntryComponent extends Manager
{

    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry
     */
    private $entry;

    public function run()
    {
        $entryIdentifier = $this->getRequest()->query->get(self :: PARAM_ENTRY_ID);

        if (! $entryIdentifier)
        {
            throw new NoObjectSelectedException(Translation :: get('Entry'));
        }

        $this->entry = $this->getDataProvider()->findEntryByIdentifier($entryIdentifier);

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->renderDetails();
//         $html[] = $this->renderReporting();
//         $html[] = $this->renderEntityTable();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    private function renderDetails()
    {
        $contentObject = $this->entry->getContentObject();

        $display = ContentObjectRenditionImplementation :: factory(
            $contentObject,
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

        $entityName = $this->getDataProvider()->getEntityNameByType($this->getEntityType());
        $entryCount = $this->getDataProvider()->countDistinctEntriesByEntityType($this->getEntityType());
        $feedbackCount = $this->getDataProvider()->countDistinctFeedbackByEntityType($this->getEntityType());
        $lateEntryCount = $this->getDataProvider()->countDistinctLateEntriesByEntityType($this->getEntityType());
        $entityCount = $this->getDataProvider()->countEntitiesByEntityType($this->getEntityType());

        $properties = array();
        $properties[Translation :: get('EntriesForEntityType', array('NAME' => $entityName))] = $entryCount . '/' .
             $entityCount;
        $properties[Translation :: get('FeedbackForEntityType', array('NAME' => $entityName))] = $feedbackCount . '/' .
             $entityCount;
        $properties[Translation :: get('LateEntriesForEntityType', array('NAME' => $entityName))] = $lateEntryCount . '/' .
             $entityCount;

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
    private function renderEntityTable()
    {
        return $this->getDataProvider()->getEntityTableForType($this, $this->getEntityType())->as_html();
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
