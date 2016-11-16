<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataManager\Implementation;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Condition\ConditionTranslator;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Database;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ResultSet\DataClassResultSet;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Variable\ConditionVariableTranslator;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * Doctrine implementation of the datamanager
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DoctrineExtension
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Doctrine\Database
     */
    private $database;

    /**
     *
     * @param DoctrineDatabase $database
     */
    public function __construct(\Chamilo\Libraries\Storage\DataManager\Doctrine\Database $database)
    {
        $this->database = $database;
    }
    
    // TODO: Fix implementation
    public function retrieve_results_by_assignment(DataClassRetrievesParameters $params)
    {
        $er_alias = $this->database->get_alias(Request::get_table_name());
        $rco_alias = \Chamilo\Core\Repository\Storage\DataManager::get_alias(ContentObject::get_table_name());
        $usr_alias = \Chamilo\Core\User\Storage\DataManager::get_alias(User::get_table_name());
        $ast_alias = \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager::get_alias(
            AssignmentSubmission::get_table_name());
        
        $query = 'SELECT ' . $er_alias . '.' . Request::PROPERTY_ID . ' AS ' . Request::PROPERTY_REQUEST_ID . ', ' .
             $er_alias . '.' . Request::PROPERTY_AUTHOR_ID . ', ' . $er_alias . '.' .
             Request::PROPERTY_CONTENT_OBJECT_ID . ', ' . $er_alias . '.' . Request::PROPERTY_COURSE_ID . ', ' .
             $er_alias . '.' . Request::PROPERTY_GUID . ', ' . $er_alias . '.' . Request::PROPERTY_PERCENTAGE . ', ' .
             $er_alias . '.' . Request::PROPERTY_PROCESS_TYPE . ', ' . $er_alias . '.' . Request::PROPERTY_REQUEST_TIME .
             ', ' . $er_alias . '.' . Request::PROPERTY_STATUS . ', ' . $er_alias . '.' .
             Request::PROPERTY_STATUS_DESCRIPTION . ', ' . $er_alias . '.' . Request::PROPERTY_VISIBLE_IN_INDEX . ', ' .
             $ast_alias . '.' . AssignmentSubmission::PROPERTY_ID . ', ' . $rco_alias . '.' .
             ContentObject::PROPERTY_TITLE . ', ' . $rco_alias . '.' . ContentObject::PROPERTY_DESCRIPTION . ', ' .
             $rco_alias . '.' . ContentObject::PROPERTY_TYPE . ', ' . $ast_alias . '.' .
             AssignmentSubmission::PROPERTY_SUBMITTER_TYPE . ', ' . $ast_alias . '.' .
             AssignmentSubmission::PROPERTY_DATE_SUBMITTED . ', ' . $usr_alias . '.' . User::PROPERTY_FIRSTNAME . ', ' .
             $usr_alias . '.' . User::PROPERTY_LASTNAME . ' FROM ';
        
        $query .= $this->create_by_assignment_base_query($params->get_condition());
        
        $order_by = $params->get_order_by();
        
        if ($order_by)
        {
            $ob = 'ORDER BY ';
            
            if ($order_by != null)
            {
                $order_by = $order_by[0];
                
                $ob .= ConditionVariableTranslator::render($order_by->get_property()) . ' ';
                
                if ($order_by->get_direction() == SORT_ASC)
                {
                    $ob .= 'ASC';
                }
                else
                {
                    $ob .= 'DESC';
                }
            }
            
            $query .= ' ' . $ob;
        }
        
        if ($params->get_offset() && $params->get_count())
        {
            $query .= ' LIMIT ' . $params->get_offset() . ',' . $params->get_count();
        }
        
        return new DataClassResultSet($this->query($query), ContentObject::class_name());
    }

    public function count_results_content_objects_by_assignment($condition)
    {
        $query = 'SELECT COUNT(*) FROM ';
        $query .= $this->create_by_assignment_base_query($condition);
        $statement = $this->query($query);
        
        if (! $statement instanceof \PDOException)
        {
            $record = $statement->fetch(\PDO::FETCH_NUM);
            
            return (int) $record[0];
        }
        else
        {
            $this->error_handling($statement);
            
            return false;
        }
    }

    private function create_by_assignment_base_query($condition)
    {
        $rdm = \Chamilo\Core\Repository\Storage\DataManager::getInstance();
        $udm = \Chamilo\Core\User\Storage\DataManager::getInstance();
        $tdm = \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager::getInstance();
        
        // ephorus_request (application\weblcms\tool\ephorus\Request)
        $er_table = Request::get_table_name();
        $er_alias = $this->database->get_alias(Request::get_table_name());
        $er_content_object_id = Database::escape_column_name(Request::PROPERTY_CONTENT_OBJECT_ID, $er_alias);
        $er_author_id = Database::escape_column_name(Request::PROPERTY_AUTHOR_ID, $er_alias);
        
        // repository_content_object (repository\ContentObject)
        $rco_table = ContentObject::get_table_name();
        $rco_alias = $rdm->get_alias(ContentObject::get_table_name());
        $rco_id = $rdm->escape_column_name(ContentObject::PROPERTY_ID, $rco_alias);
        
        // user
        $usr_table = User::get_table_name();
        $usr_alias = $udm->get_alias(User::get_table_name());
        $usr_id = $udm->escape_column_name(User::PROPERTY_ID, $usr_alias);
        
        // submission tracker
        $ast_table = AssignmentSubmission::get_table_name();
        $ast_alias = $tdm->get_alias(AssignmentSubmission::get_table_name());
        $ast_cid = $tdm->escape_column_name(AssignmentSubmission::PROPERTY_CONTENT_OBJECT_ID, $ast_alias);
        $ast_sid = $tdm->escape_column_name(AssignmentSubmission::PROPERTY_USER_ID, $ast_alias);
        
        $query = $ast_table . ' ' . $ast_alias . ' JOIN ' . $rco_table . ' ' . $rco_alias . ' ON ' . $rco_id . '=' .
             $ast_cid . ' JOIN ' . $usr_table . ' ' . $usr_alias . ' ON ' . $usr_id . '=' . $ast_sid . ' LEFT JOIN ' .
             $er_table . ' ' . $er_alias . ' ON ' . $ast_cid . '=' . $er_content_object_id . ' AND ' . $ast_sid . '=' .
             $er_author_id;
        // $join;
        
        if ($condition)
        {
            $query .= ' WHERE ' . ConditionTranslator::render($condition);
        }
        
        return $query;
    }

    private function create_by_params_base_query($condition)
    {
        $rdm = \Chamilo\Core\Repository\Storage\DataManager::getInstance();
        $udm = \Chamilo\Core\User\Storage\DataManager::getInstance();
        
        // ephorus_request (application\weblcms\tool\ephorus\Request)
        $er_table = Request::get_table_name();
        $er_alias = $this->database->get_alias(Request::get_table_name());
        $er_content_object_id = Database::escape_column_name(Request::PROPERTY_CONTENT_OBJECT_ID, $er_alias);
        $er_author_id = Database::escape_column_name(Request::PROPERTY_AUTHOR_ID, $er_alias);
        
        // repository_content_object (repository\ContentObject)
        $rco_table = ContentObject::get_table_name();
        $rco_alias = $rdm->get_alias(ContentObject::get_table_name());
        $rco_id = $rdm->escape_column_name(ContentObject::PROPERTY_ID, $rco_alias);
        
        // user
        $usr_table = User::get_table_name();
        $usr_alias = $udm->get_alias(User::get_table_name());
        $usr_id = $udm->escape_column_name(User::PROPERTY_ID, $usr_alias);
        
        $query = $er_table . ' ' . $er_alias . ' JOIN ' . $rco_table . ' ' . $rco_alias . ' ON ' . $rco_id . '=' .
             $er_content_object_id . ' JOIN ' . $usr_table . ' ' . $usr_alias . ' ON ' . $usr_id . '=' . $er_author_id;
        // $join;
        
        if ($condition)
        {
            $query .= ' WHERE ' . ConditionTranslator::render($condition);
        }
        
        return $query;
    }

    public function count_results_content_objects_by_params($condition)
    {
        $query = 'SELECT COUNT(*) FROM ';
        $query .= $this->create_by_params_base_query($condition);
        $statement = $this->query($query);
        
        if (! $statement instanceof \PDOException)
        {
            $record = $statement->fetch(\PDO::FETCH_NUM);
            
            return (int) $record[0];
        }
        else
        {
            $this->error_handling($statement);
            
            return false;
        }
    }

    public function retrieve_results_content_objects_by_params(DataClassRetrievesParameters $params)
    {
        $er_alias = $this->database->get_alias(Request::get_table_name());
        $rco_alias = \Chamilo\Core\Repository\Storage\DataManager::get_alias(ContentObject::get_table_name());
        $usr_alias = \Chamilo\Core\User\Storage\DataManager::get_alias(User::get_table_name());
        
        $query = 'SELECT ' . $er_alias . '.*, ' . $rco_alias . '.' . ContentObject::PROPERTY_TITLE . ', ' . $rco_alias .
             '.' . ContentObject::PROPERTY_DESCRIPTION . ', ' . $rco_alias . '.' . ContentObject::PROPERTY_TYPE . ', ' .
             $usr_alias . '.' . User::PROPERTY_FIRSTNAME . ', ' . $usr_alias . '.' . User::PROPERTY_LASTNAME . ' FROM ';
        $query .= $this->create_by_params_base_query($params->get_condition());
        
        $order_by = $params->get_order_by();
        
        $ob = 'ORDER BY ';
        
        if ($order_by != null)
        {
            $order_by = $order_by[0];
            $ob .= ConditionVariableTranslator::render($order_by->get_property()) . ' ';
            
            if ($order_by->get_direction() == SORT_ASC)
            {
                $ob .= 'ASC';
            }
            else
            {
                $ob .= 'DESC';
            }
        }
        
        $query .= ' ' . $ob;
        
        return new DataClassResultSet($this->query($query), ContentObject::class_name());
    }

    /**
     *
     * @param string[] $guids
     *
     * @return \libraries\storage\DataClassResultSet
     * @author Anthony Hurst (Hogeschool Gent)
     */
    public function retrieve_results_content_objects($guids)
    {
        if (! is_array($guids))
        {
            $guids = array($guids);
        }
        
        $condition = new InCondition(
            new PropertyConditionVariable(Request::class_name(), Request::PROPERTY_GUID), 
            $guids);
        
        /**
         *
         * @var \libraries\Mdb2Database $rdm
         * @var \libraries\storage\DoctrineDatabase $udm
         */
        $rdm = \Chamilo\Core\Repository\Storage\DataManager::getInstance();
        $udm = \Chamilo\Core\User\Storage\DataManager::getInstance();
        
        // Dynamic variable constants (requires the following variables for each table 'x': x_ref = 'x', x_table,
        // x_alias).
        $ref_table = '_table';
        $ref_alias = '_alias';
        
        // ephorus_request (application\weblcms\tool\ephorus\Request)
        $er_ref = 'er';
        $er_table = Request::get_table_name();
        $er_alias = $this->database->get_alias(Request::get_table_name());
        $er_guid = Database::escape_column_name(Request::PROPERTY_GUID, $er_alias);
        $er_content_object_id = Database::escape_column_name(Request::PROPERTY_CONTENT_OBJECT_ID, $er_alias);
        
        // repository_content_object (repository\ContentObject)
        $rco_ref = 'rco';
        $rco_table = ContentObject::get_table_name();
        $rco_alias = $rdm->get_alias(ContentObject::get_table_name());
        $rco_id = $rdm->escape_column_name(ContentObject::PROPERTY_ID, $rco_alias);
        
        $selects = array();
        $selects[] = $er_guid;
        
        foreach (ContentObject::get_default_property_names() as $default_property)
        {
            $selects[] = $rdm->escape_column_name($default_property, $rco_alias);
        }
        
        $select = implode(', ', $selects);
        
        $table_aliases = array();
        $table_aliases[] = $er_ref;
        $table_aliases[] = $rco_ref;
        
        $tables = array();
        
        foreach ($table_aliases as $table_alias)
        {
            $tables[${$table_alias . $ref_table}] = ${$table_alias . $ref_table} . ' AS ' . ${$table_alias . $ref_alias};
        }
        
        // Join syntax: $join_left_table JOIN $join_right_table ON $join_left_value = $join_right_value [AND
        // $conditions].
        // Condition syntax: $join_condition_column $join_condition_operator $join_condition_value.
        // $join_left_table may be null.
        $join_condition_column = 'column';
        $join_condition_operator = 'operator';
        $join_condition_value = 'value';
        $join_conditions = 'conditions';
        $join_left_table = 'left_table';
        $join_left_value = 'left_value';
        $join_right_table = 'right_table';
        $join_right_value = 'right_value';
        $join_type = 'type';
        $join_type_inner = 'JOIN';
        $join_type_left = 'LEFT JOIN';
        $join_type_outer = 'OUTER JOIN';
        $join_type_right = 'RIGHT JOIN';
        $join_declarations = array();
        $join_declarations[] = array(
            $join_left_table => $tables[$er_table], 
            $join_type => $join_type_inner, 
            $join_right_table => $tables[$rco_table], 
            $join_left_value => $er_content_object_id, 
            $join_right_value => $rco_id);
        
        $joins = array();
        
        foreach ($join_declarations as $join_declaration)
        {
            $joins_entry_parts = array();
            
            if ($join_declaration[$join_left_table])
            {
                $joins_entry_parts[] = $join_declaration[$join_left_table];
            }
            
            $joins_entry_parts[] = $join_declaration[$join_type];
            $joins_entry_parts[] = $join_declaration[$join_right_table];
            $joins_entry_parts[] = 'ON';
            $joins_entry_parts[] = $join_declaration[$join_left_value];
            $joins_entry_parts[] = '=';
            $joins_entry_parts[] = $join_declaration[$join_right_value];
            
            foreach ($join_declaration[$join_conditions] as $join_condition)
            {
                $joins_entry_parts[] = 'AND';
                $joins_entry_parts[] = $join_condition[$join_condition_column];
                $joins_entry_parts[] = $join_condition[$join_condition_operator];
                $joins_entry_parts[] = $join_condition[$join_condition_value];
            }
            
            $joins[] = implode(' ', $joins_entry_parts);
        }
        
        $join = implode(' ', $joins);
        
        $query_parts = array();
        $query_parts[] = 'SELECT';
        $query_parts[] = $select;
        $query_parts[] = 'FROM';
        $query_parts[] = $join;
        $query_parts[] = 'WHERE';
        $query_parts[] = ConditionTranslator::render($condition);
        $query = implode(' ', $query_parts);
        
        return new DataClassResultSet($this->query($query), ContentObject::class_name());
    }

    /**
     * Executes a query
     * 
     * @param string $query
     *
     * @return mixed
     */
    protected function query($query)
    {
        return $this->database->get_connection()->query($query);
    }
}
