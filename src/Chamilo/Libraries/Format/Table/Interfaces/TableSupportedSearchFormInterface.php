<?php
namespace Chamilo\Libraries\Format\Table\Interfaces;

use Chamilo\Libraries\Format\Table\Table;

/**
 * Interface to which a search form must comply if they support a table.
 * This interface will be used to connect
 * a table and a search form together so they can share their filter-parameters
 */
interface TableSupportedSearchFormInterface
{

    /**
     * Registers the table parameters in the form
     *
     * @param string[] $tableParameters
     */
    public function registerTableParametersInSearchForm(array $tableParameters = array());

    /**
     * Registers the form parameters in the table
     *
     * @param \Chamilo\Libraries\Format\Table\Table $table
     */
    public function registerSearchFormParametersInTable(Table $table);
}