<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Component;

use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Table\EntryPlagiarismResultTableParameters;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class BrowserComponent extends Manager implements TableSupport
{

    /**
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    function run()
    {
        $this->validateAccess();

        $entityType = $this->getAssignmentServiceBridge()->getCurrentEntityType();

        $parameters = new EntryPlagiarismResultTableParameters();
        $parameters->setEntityType($entityType);
        $parameters->setEntryPlagiarismResultServiceBridge($this->getEntryPlagiarismResultServiceBridge());

        $table = $this->getEntryPlagiarismResultServiceBridge()->getEntryPlagiarismResultTable(
            $entityType, $this, $parameters
        );

        /** @var int $count - This is the absolute count without taking search parameters into account */
        $count = $this->getEntryPlagiarismResultServiceBridge()->countEntriesWithPlagiarismResult(
            $entityType, new FilterParameters()
        );

        $checkEntriesUrl = $this->get_url([self::PARAM_ACTION => self::ACTION_CHECK_ALL_ENTRIES]);

        $toolbar = new ButtonToolBar($this->get_url());
        $toolbar->addItem(
            new Button(
                $this->getTranslator()->trans('CheckAllEntries', [], Manager::context()),
                new FontAwesomeGlyph('files-o'),
                $checkEntriesUrl
            )
        );

        $renderer = new ButtonToolBarRenderer($toolbar);
        $table->setSearchForm($renderer->getSearchForm());

        return $this->getTwig()->render(
            Manager::context() . ':Browser.html.twig',
            [
                'HEADER' => $this->render_header(), 'FOOTER' => $this->render_footer(), 'TABLE' => $table->render(),
                'COUNT' => $count, 'CHECK_ALL_ENTRIES_URL' => $checkEntriesUrl, 'TOOLBAR' => $renderer->render()
            ]
        );
    }

    /**
     * Returns the condition
     *
     * @param string $tableClassname
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    public function get_table_condition($tableClassname)
    {
        return null;
    }
}
