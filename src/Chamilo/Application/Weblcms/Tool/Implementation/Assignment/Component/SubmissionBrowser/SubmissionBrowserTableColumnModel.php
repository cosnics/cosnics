<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionBrowser;

use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Libraries\Format\Table\Column\SortableStaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Platform\Translation;

/**
 * Abstract table column model for the submitters browser table
 * 
 * @package application.weblcms.tool.assignment.php.component.submission_browser
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Anthony Hurst (Hogeschool Gent)
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
abstract class SubmissionBrowserTableColumnModel extends RecordTableColumnModel implements 
    TableColumnModelActionsColumnSupport
{

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(
            new SortableStaticTableColumn(
                Manager::PROPERTY_FIRST_SUBMISSION, 
                $this->getTranslation(Manager::PROPERTY_FIRST_SUBMISSION)));
        
        $this->add_column(
            new SortableStaticTableColumn(
                Manager::PROPERTY_LAST_SUBMISSION, 
                $this->getTranslation(Manager::PROPERTY_LAST_SUBMISSION)));
        
        $this->add_column(
            new SortableStaticTableColumn(
                Manager::PROPERTY_NUMBER_OF_SUBMISSIONS, 
                $this->getTranslation(Manager::PROPERTY_NUMBER_OF_SUBMISSIONS)));
        
        $this->add_column(
            new StaticTableColumn(
                Manager :: PROPERTY_NUMBER_OF_FEEDBACKS,
                $this->getTranslation(Manager :: PROPERTY_NUMBER_OF_FEEDBACKS)));
    }

    /**
     * Helper functionality
     * 
     * @param string $variableName
     * @param array $parameters
     *
     * @return string
     */
    protected function getTranslation($variableName, $parameters = array())
    {
        return Translation::getInstance()->getTranslation($variableName, $parameters, Manager::context());
    }
}
