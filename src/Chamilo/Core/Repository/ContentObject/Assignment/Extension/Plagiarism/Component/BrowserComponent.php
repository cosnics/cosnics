<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Component;

use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Table\EntryPlagiarismResultTableParameters;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;

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
        if (!$this->getAssignmentServiceBridge()->canEditAssignment())
        {
            throw new NotAllowedException();
        }

        $entityType = $this->getAssignmentServiceBridge()->getCurrentEntityType();

        $parameters = new EntryPlagiarismResultTableParameters();
        $parameters->setEntityType($entityType);
        $parameters->setEntryPlagiarismResultServiceBridge($this->getEntryPlagiarismResultServiceBridge());

        $table = $this->getEntryPlagiarismResultServiceBridge()->getEntryPlagiarismResultTable(
            $entityType, $this, $parameters
        );

        $count = $this->getEntryPlagiarismResultServiceBridge()->countEntriesWithPlagiarismResult($entityType);
        $checkEntriesUrl = $this->get_url([self::PARAM_ACTION => self::ACTION_CHECK_ALL_ENTRIES]);

        return $this->getTwig()->render(
            Manager::context() . ':Browser.html.twig',
            [
                'HEADER' => $this->render_header(), 'FOOTER' => $this->render_footer(), 'TABLE' => $table->render(),
                'COUNT' => $count, 'CHECK_ALL_ENTRIES_URL' => $checkEntriesUrl
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