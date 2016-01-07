<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Form\NoteForm;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Form\ScoreForm;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Format\Table\PropertiesTable;
use Chamilo\Libraries\Format\Tabs\DynamicContentTab;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

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

    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Form\ScoreForm
     */
    private $scoreForm;

    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Form\NoteForm
     */
    private $noteForm;

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
        $html[] = $this->renderTabs();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function renderTabs()
    {
        $tabsRenderer = new DynamicTabsRenderer('entry');

        $tabsRenderer->add_tab(
            new DynamicContentTab(
                'details',
                Translation :: get('DetailsFeedback'),
                Theme :: getInstance()->getImagePath(self :: package(), 'Tab/Details'),
                $this->renderDetails()));

        $tabsRenderer->add_tab(
            new DynamicContentTab(
                'entry',
                Translation :: get('Entry'),
                Theme :: getInstance()->getImagePath(self :: package(), 'Tab/Entry'),
                $this->renderContentObject()));

        return $tabsRenderer->render();
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     *
     * @return string
     */
    protected function renderContentObject()
    {
        $contentObject = $this->getEntry()->getContentObject();

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
    protected function renderDetails()
    {
        $dateFormat = Translation :: get('DateTimeFormatLong', null, Utilities :: COMMON_LIBRARIES);
        $submittedDate = DatetimeUtilities :: format_locale_date($dateFormat, $this->getEntry()->getSubmitted());

        $html = array();

        $properties = array();
        $properties[Translation :: get('Submitted')] = $submittedDate;

        $entityRenderer = $this->getDataProvider()->getEntityRendererForEntityTypeAndId(
            $this,
            $this->getEntry()->getEntityType(),
            $this->getEntry()->getEntityId());

        $properties = array_merge($properties, $entityRenderer->getProperties());

        $properties[Translation :: get('Score')] = $this->getScoreForm()->render();
        $properties[Translation :: get('Note')] = $this->getNoteForm()->render();

        $table = new PropertiesTable($properties);

        $html[] = $table->toHtml();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Form\ScoreForm
     */
    protected function getScoreForm()
    {
        if (! isset($this->scoreForm))
        {
            $this->scoreForm = new ScoreForm($this->getEntry(), $this->getDataProvider(), $this->get_url());
        }

        return $this->scoreForm;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Form\ScoreForm
     */
    protected function getNoteForm()
    {
        if (! isset($this->noteForm))
        {
            $this->noteForm = new NoteForm($this->getEntry(), $this->getDataProvider(), $this->get_url());
        }

        return $this->noteForm;
    }
}
