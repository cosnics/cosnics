<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\Query;

use Chamilo\Libraries\Storage\DataManager\AdoDb\Query\Expression\CompositeExpression;

/**
 * Based on the Doctrine DBAL Query-builder architecture
 *
 * @link www.doctrine-project.org
 * @since 2.1
 * @author Guilherme Blanco <guilhermeblanco@hotmail.com>
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @license MIT
 */
class QueryBuilder
{
    /*
     * The query types.
     */
    const SELECT = 0;
    const DELETE = 1;
    const UPDATE = 2;
    const INSERT = 3;

    /*
     * The builder states.
     */
    const STATE_DIRTY = 0;
    const STATE_CLEAN = 1;

    /**
     *
     * @var array The array of SQL parts collected.
     */
    private $sqlParts = array(
        'select' => array(),
        'from' => array(),
        'join' => array(),
        'set' => array(),
        'where' => null,
        'groupBy' => array(),
        'having' => null,
        'orderBy' => array(),
        'values' => array());

    /**
     * The complete SQL string for this query.
     *
     * @var string
     */
    private $sql;

    /**
     * The type of query this is.
     * Can be select, update or delete.
     *
     * @var integer
     */
    private $type = self::SELECT;

    /**
     * The state of the query object.
     * Can be dirty or clean.
     *
     * @var integer
     */
    private $state = self::STATE_CLEAN;

    /**
     * Gets the type of the currently built query.
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Gets the state of this query builder instance.
     *
     * @return integer Either QueryBuilder::STATE_DIRTY or QueryBuilder::STATE_CLEAN.
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     *
     * @return string The SQL query string.
     */
    public function getSQL()
    {
        if ($this->sql !== null && $this->state === self::STATE_CLEAN)
        {
            return $this->sql;
        }

        switch ($this->type)
        {
            case self::INSERT :
                $sql = $this->getSQLForInsert();
                break;
            case self::DELETE :
                $sql = $this->getSQLForDelete();
                break;

            case self::UPDATE :
                $sql = $this->getSQLForUpdate();
                break;

            case self::SELECT :
            default :
                $sql = $this->getSQLForSelect();
                break;
        }

        $this->state = self::STATE_CLEAN;
        $this->sql = $sql;

        return $sql;
    }

    /**
     * Either appends to or replaces a single, generic query part.
     * The available parts are: 'select', 'from', 'set', 'where',
     * 'groupBy', 'having' and 'orderBy'.
     *
     * @param string $sqlPartName
     * @param string $sqlPart
     * @param boolean $append
     *
     * @return \Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryBuilder This QueryBuilder instance.
     */
    public function add($sqlPartName, $sqlPart, $append = false)
    {
        $isArray = is_array($sqlPart);
        $isMultiple = is_array($this->sqlParts[$sqlPartName]);

        if ($isMultiple && ! $isArray)
        {
            $sqlPart = array($sqlPart);
        }

        $this->state = self::STATE_DIRTY;

        if ($append)
        {
            if ($sqlPartName == "orderBy" || $sqlPartName == "groupBy" || $sqlPartName == "select" ||
                 $sqlPartName == "set")
            {
                foreach ($sqlPart as $part)
                {
                    $this->sqlParts[$sqlPartName][] = $part;
                }
            }
            elseif ($isArray && is_array($sqlPart[key($sqlPart)]))
            {
                $key = key($sqlPart);
                $this->sqlParts[$sqlPartName][$key][] = $sqlPart[$key];
            }
            elseif ($isMultiple)
            {
                $this->sqlParts[$sqlPartName][] = $sqlPart;
            }
            else
            {
                $this->sqlParts[$sqlPartName] = $sqlPart;
            }

            return $this;
        }

        $this->sqlParts[$sqlPartName] = $sqlPart;

        return $this;
    }

    /**
     *
     * @param mixed $select The selection expressions.
     * @return \Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryBuilder This QueryBuilder instance.
     */
    public function select($select = null)
    {
        $this->type = self::SELECT;

        if (empty($select))
        {
            return $this;
        }

        $selects = is_array($select) ? $select : func_get_args();

        return $this->add('select', $selects, false);
    }

    /**
     *
     * @param mixed $select The selection expression.
     * @return \Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryBuilder This QueryBuilder instance.
     */
    public function addSelect($select = null)
    {
        $this->type = self::SELECT;

        if (empty($select))
        {
            return $this;
        }

        $selects = is_array($select) ? $select : func_get_args();

        return $this->add('select', $selects, true);
    }

    /**
     *
     * @param string $delete The table whose rows are subject to the deletion.
     * @param string $alias The table alias used in the constructed query.
     * @return \Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryBuilder This QueryBuilder instance.
     */
    public function delete($delete = null, $alias = null)
    {
        $this->type = self::DELETE;

        if (! $delete)
        {
            return $this;
        }

        return $this->add('from', array('table' => $delete, 'alias' => $alias));
    }

    /**
     *
     * @param string $update The table whose rows are subject to the update.
     * @param string $alias The table alias used in the constructed query.
     * @return \Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryBuilder This QueryBuilder instance.
     */
    public function update($update = null, $alias = null)
    {
        $this->type = self::UPDATE;

        if (! $update)
        {
            return $this;
        }

        return $this->add('from', array('table' => $update, 'alias' => $alias));
    }

    /**
     *
     * @param string $insert The table into which the rows should be inserted.
     * @return QueryBuilder This QueryBuilder instance.
     */
    public function insert($insert = null)
    {
        $this->type = self::INSERT;

        if (! $insert)
        {
            return $this;
        }

        return $this->add('from', array('table' => $insert));
    }

    /**
     *
     * @param string $from The table.
     * @param string|null $alias The alias of the table.
     * @return QueryBuilder This QueryBuilder instance.
     */
    public function from($from, $alias = null)
    {
        return $this->add('from', array('table' => $from, 'alias' => $alias), true);
    }

    /**
     *
     * @param string $fromAlias The alias that points to a from clause.
     * @param string $join The table name to join.
     * @param string $alias The alias of the join table.
     * @param string $condition The condition for the join.
     * @return \Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryBuilder This QueryBuilder instance.
     */
    public function join($fromAlias, $join, $alias, $condition = null)
    {
        return $this->innerJoin($fromAlias, $join, $alias, $condition);
    }

    /**
     *
     * @param string $fromAlias The alias that points to a from clause.
     * @param string $join The table name to join.
     * @param string $alias The alias of the join table.
     * @param string $condition The condition for the join.
     * @return \Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryBuilder This QueryBuilder instance.
     */
    public function innerJoin($fromAlias, $join, $alias, $condition = null)
    {
        return $this->add(
            'join',
            array(
                $fromAlias => array(
                    'joinType' => 'inner',
                    'joinTable' => $join,
                    'joinAlias' => $alias,
                    'joinCondition' => $condition)),
            true);
    }

    /**
     *
     * @param string $fromAlias The alias that points to a from clause.
     * @param string $join The table name to join.
     * @param string $alias The alias of the join table.
     * @param string $condition The condition for the join.
     * @return \Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryBuilder This QueryBuilder instance.
     */
    public function leftJoin($fromAlias, $join, $alias, $condition = null)
    {
        return $this->add(
            'join',
            array(
                $fromAlias => array(
                    'joinType' => 'left',
                    'joinTable' => $join,
                    'joinAlias' => $alias,
                    'joinCondition' => $condition)),
            true);
    }

    /**
     *
     * @param string $fromAlias The alias that points to a from clause.
     * @param string $join The table name to join.
     * @param string $alias The alias of the join table.
     * @param string $condition The condition for the join.
     * @return \Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryBuilder This QueryBuilder instance.
     */
    public function rightJoin($fromAlias, $join, $alias, $condition = null)
    {
        return $this->add(
            'join',
            array(
                $fromAlias => array(
                    'joinType' => 'right',
                    'joinTable' => $join,
                    'joinAlias' => $alias,
                    'joinCondition' => $condition)),
            true);
    }

    /**
     *
     * @param string $key The column to set.
     * @param string $value The value, expression, placeholder, etc.
     * @return \Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryBuilder This QueryBuilder instance.
     */
    public function set($key, $value)
    {
        return $this->add('set', $key . ' = ' . $value, true);
    }

    /**
     *
     * @param mixed $predicates The restriction predicates.
     * @return \Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryBuilder This QueryBuilder instance.
     */
    public function where($predicates)
    {
        if (! (func_num_args() == 1 && $predicates instanceof CompositeExpression))
        {
            $predicates = new CompositeExpression(CompositeExpression::TYPE_AND, func_get_args());
        }

        return $this->add('where', $predicates);
    }

    /**
     *
     * @param mixed $where The query restrictions.
     * @return \Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryBuilder This QueryBuilder instance.
     * @see where()
     */
    public function andWhere($where)
    {
        $args = func_get_args();
        $where = $this->getQueryPart('where');

        if ($where instanceof CompositeExpression && $where->getType() === CompositeExpression::TYPE_AND)
        {
            $where->addMultiple($args);
        }
        else
        {
            array_unshift($args, $where);
            $where = new CompositeExpression(CompositeExpression::TYPE_AND, $args);
        }

        return $this->add('where', $where, true);
    }

    /**
     *
     * @param mixed $where The WHERE statement.
     * @return \Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryBuilder This QueryBuilder instance.
     * @see where()
     */
    public function orWhere($where)
    {
        $args = func_get_args();
        $where = $this->getQueryPart('where');

        if ($where instanceof CompositeExpression && $where->getType() === CompositeExpression::TYPE_OR)
        {
            $where->addMultiple($args);
        }
        else
        {
            array_unshift($args, $where);
            $where = new CompositeExpression(CompositeExpression::TYPE_OR, $args);
        }

        return $this->add('where', $where, true);
    }

    /**
     *
     * @param mixed $groupBy The grouping expression.
     * @return \Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryBuilder This QueryBuilder instance.
     */
    public function groupBy($groupBy)
    {
        if (empty($groupBy))
        {
            return $this;
        }

        $groupBy = is_array($groupBy) ? $groupBy : func_get_args();

        return $this->add('groupBy', $groupBy, false);
    }

    /**
     *
     * @param mixed $groupBy The grouping expression.
     * @return \Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryBuilder This QueryBuilder instance.
     */
    public function addGroupBy($groupBy)
    {
        if (empty($groupBy))
        {
            return $this;
        }

        $groupBy = is_array($groupBy) ? $groupBy : func_get_args();

        return $this->add('groupBy', $groupBy, true);
    }

    /**
     *
     * @param string $column The column into which the value should be inserted.
     * @param string $value The value that should be inserted into the column.
     * @return QueryBuilder This QueryBuilder instance.
     */
    public function setValue($column, $value)
    {
        $this->sqlParts['values'][$column] = $value;

        return $this;
    }

    /**
     *
     * @param array $values The values to specify for the insert query indexed by column names.
     * @return QueryBuilder This QueryBuilder instance.
     */
    public function values(array $values)
    {
        return $this->add('values', $values);
    }

    /**
     *
     * @param mixed $having The restriction over the groups.
     * @return \Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryBuilder This QueryBuilder instance.
     */
    public function having($having)
    {
        if (! (func_num_args() == 1 && $having instanceof CompositeExpression))
        {
            $having = new CompositeExpression(CompositeExpression::TYPE_AND, func_get_args());
        }

        return $this->add('having', $having);
    }

    /**
     *
     * @param mixed $having The restriction to append.
     * @return \Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryBuilder This QueryBuilder instance.
     */
    public function andHaving($having)
    {
        $args = func_get_args();
        $having = $this->getQueryPart('having');

        if ($having instanceof CompositeExpression && $having->getType() === CompositeExpression::TYPE_AND)
        {
            $having->addMultiple($args);
        }
        else
        {
            array_unshift($args, $having);
            $having = new CompositeExpression(CompositeExpression::TYPE_AND, $args);
        }

        return $this->add('having', $having);
    }

    /**
     *
     * @param mixed $having The restriction to add.
     * @return \Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryBuilder This QueryBuilder instance.
     */
    public function orHaving($having)
    {
        $args = func_get_args();
        $having = $this->getQueryPart('having');

        if ($having instanceof CompositeExpression && $having->getType() === CompositeExpression::TYPE_OR)
        {
            $having->addMultiple($args);
        }
        else
        {
            array_unshift($args, $having);
            $having = new CompositeExpression(CompositeExpression::TYPE_OR, $args);
        }

        return $this->add('having', $having);
    }

    /**
     *
     * @param string $sort The ordering expression.
     * @param string $order The ordering direction.
     * @return \Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryBuilder This QueryBuilder instance.
     */
    public function orderBy($sort, $order = null)
    {
        return $this->add('orderBy', $sort . ' ' . (! $order ? 'ASC' : $order), false);
    }

    /**
     *
     * @param string $sort The ordering expression.
     * @param string $order The ordering direction.
     * @return \Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryBuilder This QueryBuilder instance.
     */
    public function addOrderBy($sort, $order = null)
    {
        return $this->add('orderBy', $sort . ' ' . (! $order ? 'ASC' : $order), true);
    }

    /**
     *
     * @param string $queryPartName
     * @return mixed
     */
    public function getQueryPart($queryPartName)
    {
        return $this->sqlParts[$queryPartName];
    }

    /**
     *
     * @return array
     */
    public function getQueryParts()
    {
        return $this->sqlParts;
    }

    /**
     *
     * @param array|null $queryPartNames
     * @return \Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryBuilder This QueryBuilder instance.
     */
    public function resetQueryParts($queryPartNames = null)
    {
        if (is_null($queryPartNames))
        {
            $queryPartNames = array_keys($this->sqlParts);
        }

        foreach ($queryPartNames as $queryPartName)
        {
            $this->resetQueryPart($queryPartName);
        }

        return $this;
    }

    /**
     *
     * @param string $queryPartName
     * @return \Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryBuilder This QueryBuilder instance.
     */
    public function resetQueryPart($queryPartName)
    {
        $this->sqlParts[$queryPartName] = is_array($this->sqlParts[$queryPartName]) ? array() : null;

        $this->state = self::STATE_DIRTY;

        return $this;
    }

    /**
     *
     * @return string
     * @throws \Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryException
     */
    private function getSQLForSelect()
    {
        $query = 'SELECT ' . implode(', ', $this->sqlParts['select']) . ' FROM ';

        $query .= implode(', ', $this->getFromClauses()) .
             ($this->sqlParts['where'] !== null ? ' WHERE ' . ((string) $this->sqlParts['where']) : '') .
             ($this->sqlParts['groupBy'] ? ' GROUP BY ' . implode(', ', $this->sqlParts['groupBy']) : '') .
             ($this->sqlParts['having'] !== null ? ' HAVING ' . ((string) $this->sqlParts['having']) : '') .
             ($this->sqlParts['orderBy'] ? ' ORDER BY ' . implode(', ', $this->sqlParts['orderBy']) : '');

        return $query;
    }

    /**
     *
     * @return string[]
     */
    private function getFromClauses()
    {
        $fromClauses = array();
        $knownAliases = array();

        // Loop through all FROM clauses
        foreach ($this->sqlParts['from'] as $from)
        {
            if ($from['alias'] === null)
            {
                $tableSql = $from['table'];
                $tableReference = $from['table'];
            }
            else
            {
                $tableSql = $from['table'] . ' ' . $from['alias'];
                $tableReference = $from['alias'];
            }

            $knownAliases[$tableReference] = true;

            $fromClauses[$tableReference] = $tableSql . $this->getSQLForJoins($tableReference, $knownAliases);
        }

        $this->verifyAllAliasesAreKnown($knownAliases);

        return $fromClauses;
    }

    /**
     *
     * @param array $knownAliases
     * @throws QueryException
     */
    private function verifyAllAliasesAreKnown(array $knownAliases)
    {
        foreach ($this->sqlParts['join'] as $fromAlias => $joins)
        {
            if (! isset($knownAliases[$fromAlias]))
            {
                throw QueryException::unknownAlias($fromAlias, array_keys($knownAliases));
            }
        }
    }

    /**
     *
     * @return string
     */
    private function getSQLForInsert()
    {
        return 'INSERT INTO ' . $this->sqlParts['from']['table'] . ' (' .
             implode(', ', array_keys($this->sqlParts['values'])) . ')' . ' VALUES(' .
             implode(', ', $this->sqlParts['values']) . ')';
    }

    /**
     *
     * @return string
     */
    private function getSQLForUpdate()
    {
        $table = $this->sqlParts['from']['table'] .
             ($this->sqlParts['from']['alias'] ? ' ' . $this->sqlParts['from']['alias'] : '');
        $query = 'UPDATE ' . $table . ' SET ' . implode(", ", $this->sqlParts['set']) .
             ($this->sqlParts['where'] !== null ? ' WHERE ' . ((string) $this->sqlParts['where']) : '');

        return $query;
    }

    /**
     *
     * @return string
     */
    private function getSQLForDelete()
    {
        $table = $this->sqlParts['from']['table'] .
             ($this->sqlParts['from']['alias'] ? ' ' . $this->sqlParts['from']['alias'] : '');
        $query = 'DELETE FROM ' . $table .
             ($this->sqlParts['where'] !== null ? ' WHERE ' . ((string) $this->sqlParts['where']) : '');

        return $query;
    }

    /**
     *
     * @return string The string representation of this QueryBuilder.
     */
    public function __toString()
    {
        return $this->getSQL();
    }

    /**
     *
     * @param string $fromAlias
     * @param array $knownAliases
     * @return string
     */
    private function getSQLForJoins($fromAlias, array &$knownAliases)
    {
        $sql = '';

        if (isset($this->sqlParts['join'][$fromAlias]))
        {
            foreach ($this->sqlParts['join'][$fromAlias] as $join)
            {
                if (array_key_exists($join['joinAlias'], $knownAliases))
                {
                    throw QueryException::nonUniqueAlias($join['joinAlias'], array_keys($knownAliases));
                }
                $sql .= ' ' . strtoupper($join['joinType']) . ' JOIN ' . $join['joinTable'] . ' ' . $join['joinAlias'] .
                     ' ON ' . ((string) $join['joinCondition']);
                $knownAliases[$join['joinAlias']] = true;
            }

            foreach ($this->sqlParts['join'][$fromAlias] as $join)
            {
                $sql .= $this->getSQLForJoins($join['joinAlias'], $knownAliases);
            }
        }

        return $sql;
    }

    /**
     *
     * @return void
     */
    public function __clone()
    {
        foreach ($this->sqlParts as $part => $elements)
        {
            if (is_array($this->sqlParts[$part]))
            {
                foreach ($this->sqlParts[$part] as $idx => $element)
                {
                    if (is_object($element))
                    {
                        $this->sqlParts[$part][$idx] = clone $element;
                    }
                }
            }
            elseif (is_object($elements))
            {
                $this->sqlParts[$part] = clone $elements;
            }
        }
    }
}
