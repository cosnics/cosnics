<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Table\AsessmentAttempt;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Storage\DataClass\Publication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class represents a column model for the attempts of an assessment
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssessmentAttemptTableColumnModel extends RecordTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const DEFAULT_ORDER_COLUMN_INDEX = 1;

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(User::class, User::PROPERTY_FIRSTNAME));
        $this->add_column(new DataClassPropertyTableColumn(User::class, User::PROPERTY_LASTNAME));
        $this->add_column(new DataClassPropertyTableColumn(User::class, User::PROPERTY_OFFICIAL_CODE));
        
        $this->add_column(
            new DataClassPropertyTableColumn(AssessmentAttempt::class, AssessmentAttempt::PROPERTY_START_TIME));
        
        $this->add_column(
            new DataClassPropertyTableColumn(AssessmentAttempt::class, AssessmentAttempt::PROPERTY_END_TIME));
        
        $this->add_column(
            new DataClassPropertyTableColumn(AssessmentAttempt::class, AssessmentAttempt::PROPERTY_TOTAL_TIME));
        
        $publication = $this->get_component()->get_publication();
        $parameters = new DataClassRetrieveParameters(
            new EqualityCondition(
                new PropertyConditionVariable(Publication::class, Publication::PROPERTY_PUBLICATION_ID),
                new StaticConditionVariable($publication->get_id())));
        $assessment_publication = DataManager::retrieve(Publication::class, $parameters);
        
        if ($this->get_component()->is_allowed(WeblcmsRights::EDIT_RIGHT, $publication) ||
             $assessment_publication->get_configuration()->show_score())
        {
            $this->add_column(
                new DataClassPropertyTableColumn(
                    AssessmentAttempt::class,
                    AssessmentAttempt::PROPERTY_TOTAL_SCORE));
        }
        
        $this->add_column(
            new DataClassPropertyTableColumn(AssessmentAttempt::class, AssessmentAttempt::PROPERTY_STATUS));
    }
}