<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Table\PropertiesTable;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ViewerComponent extends Manager implements TableSupport
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    public function run()
    {
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = $this->buttonToolbarRenderer->render();
        $html[] = $this->renderDetails();
        $html[] = $this->renderReporting();
        $html[] = $this->renderEntityTable();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    protected function renderDetails()
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
    protected function renderReporting()
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
    protected function renderEntityTable()
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

    protected function getButtonToolbarRenderer()
    {
        if (! isset($this->buttonToolbarRenderer))
        {
            $buttonToolBar = new ButtonToolBar();
            $buttonToolBar->addButtonGroup(
                new ButtonGroup(
                    array(
                        new Button(
                            Translation :: get('Download'), 
                            Theme :: getInstance()->getCommonImagePath('Action/Download')), 
                        new Button(
                            Translation :: get('SubmissionSubmit'), 
                            Theme :: getInstance()->getCommonImagePath('Action/Add')))));
            
            $buttonToolBar->addButtonGroup(
                new ButtonGroup(
                    array(
                        new Button(
                            Translation :: get('ScoreOverview'), 
                            Theme :: getInstance()->getCommonImagePath('Action/Statistics')), 
                        new Button(
                            Translation :: get('EntriesOverview'), 
                            Theme :: getInstance()->getCommonImagePath('Action/Statistics')))));
            
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);
        }
        
        return $this->buttonToolbarRenderer;
    }
}
