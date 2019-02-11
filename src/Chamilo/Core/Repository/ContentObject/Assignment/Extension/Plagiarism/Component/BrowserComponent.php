<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Component;

use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Table\EntryPlagiarismResultTableParameters;
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
     */
    function run()
    {
        $parameters = new EntryPlagiarismResultTableParameters();
        $parameters->setEntryPlagiarismResultServiceBridge($this->getEntryPlagiarismResultServiceBridge());

        $table = $this->getEntryPlagiarismResultServiceBridge()->getEntryPlagiarismResultTable(
            $this->getAssignmentServiceBridge()->getCurrentEntityType(), $this, $parameters
        );

        return $this->getTwig()->render(
            Manager::context() . ':Browser.html.twig',
            ['HEADER' => $this->render_header(), 'FOOTER' => $this->render_footer(), 'TABLE' => $table->render()]
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
        // TODO: Implement get_table_condition() method.
}}