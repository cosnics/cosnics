<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\Query\Expression;

use ADOConnection;
use function func_get_arg;
use function func_get_args;
use function func_num_args;
use function implode;
use function sprintf;

/**
 * Based on the Doctrine DBAL Query-builder architecture
 * ExpressionBuilder class is responsible to dynamically create SQL query parts.
 *
 * @link www.doctrine-project.org
 * @version 3.3.6
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @license MIT
 */
class ExpressionBuilder
{
    public const EQ = '=';
    public const GT = '>';
    public const GTE = '>=';
    public const LT = '<';
    public const LTE = '<=';
    public const NEQ = '<>';

    /**
     * The DBAL Connection.
     *
     * @var \ADOConnection
     */
    private $connection;

    /**
     * Initializes a new <tt>ExpressionBuilder</tt>.
     *
     * @param \ADOConnection $connection The AdoDB Connection.
     */
    public function __construct(ADOConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Creates a conjunction of the given expressions.
     *
     * @param string|CompositeExpression $expression
     * @param string|CompositeExpression ...$expressions
     */
    public function and($expression, ...$expressions): CompositeExpression
    {
        return CompositeExpression::and($expression, ...$expressions);
    }

    /**
     * @param mixed $x Optional clause. Defaults = null, but requires
     *        at least one defined when converting to string.
     *
     * @return CompositeExpression
     * @deprecated Use `and()` instead.
     *
     */
    public function andX($x = null)
    {
        return new CompositeExpression(CompositeExpression::TYPE_AND, func_get_args());
    }

    /**
     * Creates a comparison expression.
     *
     * @param mixed $x The left expression.
     * @param string $operator One of the ExpressionBuilder::* constants.
     * @param mixed $y The right expression.
     *
     * @return string
     */
    public function comparison($x, $operator, $y)
    {
        return $x . ' ' . $operator . ' ' . $y;
    }

    /**
     * Creates an equality comparison expression with the given arguments.
     *
     * First argument is considered the left expression and the second is the right expression.
     * When converted to string, it will generated a <left expr> = <right expr>. Example:
     *
     * [php]
     * // u.id = ?
     * $expr->eq('u.id', '?');
     *
     * @param mixed $x The left expression.
     * @param mixed $y The right expression.
     *
     * @return string
     */
    public function eq($x, $y)
    {
        return $this->comparison($x, self::EQ, $y);
    }

    /**
     * Creates a greater-than comparison expression with the given arguments.
     * First argument is considered the left expression and the second is the right expression.
     * When converted to string, it will generated a <left expr> > <right expr>. Example:
     *
     * [php]
     * // u.id > ?
     * $q->where($q->expr()->gt('u.id', '?'));
     *
     * @param mixed $x The left expression.
     * @param mixed $y The right expression.
     *
     * @return string
     */
    public function gt($x, $y)
    {
        return $this->comparison($x, self::GT, $y);
    }

    /**
     * Creates a greater-than-equal comparison expression with the given arguments.
     * First argument is considered the left expression and the second is the right expression.
     * When converted to string, it will generated a <left expr> >= <right expr>. Example:
     *
     * [php]
     * // u.id >= ?
     * $q->where($q->expr()->gte('u.id', '?'));
     *
     * @param mixed $x The left expression.
     * @param mixed $y The right expression.
     *
     * @return string
     */
    public function gte($x, $y)
    {
        return $this->comparison($x, self::GTE, $y);
    }

    /**
     * Creates an IN () comparison expression with the given arguments.
     *
     * @param string $x The SQL expression to be matched against the set.
     * @param string|string[] $y The SQL expression or an array of SQL expressions representing the set.
     *
     * @return string
     */
    public function in($x, $y)
    {
        return $this->comparison($x, 'IN', '(' . implode(', ', (array) $y) . ')');
    }

    /**
     * Creates an IS NOT NULL expression with the given arguments.
     *
     * @param string $x The expression to be restricted by IS NOT NULL.
     *
     * @return string
     */
    public function isNotNull($x)
    {
        return $x . ' IS NOT NULL';
    }

    /**
     * Creates an IS NULL expression with the given arguments.
     *
     * @param string $x The expression to be restricted by IS NULL.
     *
     * @return string
     */
    public function isNull($x)
    {
        return $x . ' IS NULL';
    }

    /**
     * Creates a LIKE() comparison expression with the given arguments.
     *
     * @param string $x The expression to be inspected by the LIKE comparison
     * @param mixed $y The pattern to compare against
     *
     * @return string
     */
    public function like($x, $y/*, ?string $escapeChar = null */)
    {
        return $this->comparison($x, 'LIKE', $y) . (func_num_args() >= 3 ? sprintf(' ESCAPE %s', func_get_arg(2)) : '');
    }

    /**
     * Builds an SQL literal from a given input parameter.
     *
     * The usage of this method is discouraged. Use prepared statements
     * or {@see AbstractPlatform::quoteStringLiteral()} instead.
     *
     * @param mixed $input The parameter to be quoted.
     * @param int|null $type The type of the parameter.
     *
     * @return string
     */
    public function literal($input, $type = null)
    {
        return $this->connection->quote($input, $type);
    }

    /**
     * Creates a lower-than comparison expression with the given arguments.
     * First argument is considered the left expression and the second is the right expression.
     * When converted to string, it will generated a <left expr> < <right expr>. Example:
     *
     * [php]
     * // u.id < ?
     * $q->where($q->expr()->lt('u.id', '?'));
     *
     * @param mixed $x The left expression.
     * @param mixed $y The right expression.
     *
     * @return string
     */
    public function lt($x, $y)
    {
        return $this->comparison($x, self::LT, $y);
    }

    /**
     * Creates a lower-than-equal comparison expression with the given arguments.
     * First argument is considered the left expression and the second is the right expression.
     * When converted to string, it will generated a <left expr> <= <right expr>. Example:
     *
     * [php]
     * // u.id <= ?
     * $q->where($q->expr()->lte('u.id', '?'));
     *
     * @param mixed $x The left expression.
     * @param mixed $y The right expression.
     *
     * @return string
     */
    public function lte($x, $y)
    {
        return $this->comparison($x, self::LTE, $y);
    }

    /**
     * Creates a non equality comparison expression with the given arguments.
     * First argument is considered the left expression and the second is the right expression.
     * When converted to string, it will generated a <left expr> <> <right expr>. Example:
     *
     * [php]
     * // u.id <> 1
     * $q->where($q->expr()->neq('u.id', '1'));
     *
     * @param mixed $x The left expression.
     * @param mixed $y The right expression.
     *
     * @return string
     */
    public function neq($x, $y)
    {
        return $this->comparison($x, self::NEQ, $y);
    }

    /**
     * Creates a NOT IN () comparison expression with the given arguments.
     *
     * @param string $x The SQL expression to be matched against the set.
     * @param string|string[] $y The SQL expression or an array of SQL expressions representing the set.
     *
     * @return string
     */
    public function notIn($x, $y)
    {
        return $this->comparison($x, 'NOT IN', '(' . implode(', ', (array) $y) . ')');
    }

    /**
     * Creates a NOT LIKE() comparison expression with the given arguments.
     *
     * @param string $x The expression to be inspected by the NOT LIKE comparison
     * @param mixed $y The pattern to compare against
     *
     * @return string
     */
    public function notLike($x, $y/*, ?string $escapeChar = null */)
    {
        return $this->comparison($x, 'NOT LIKE', $y) .
            (func_num_args() >= 3 ? sprintf(' ESCAPE %s', func_get_arg(2)) : '');
    }

    /**
     * Creates a disjunction of the given expressions.
     *
     * @param string|CompositeExpression $expression
     * @param string|CompositeExpression ...$expressions
     */
    public function or($expression, ...$expressions): CompositeExpression
    {
        return CompositeExpression::or($expression, ...$expressions);
    }

    /**
     * @param mixed $x Optional clause. Defaults = null, but requires
     *        at least one defined when converting to string.
     *
     * @return CompositeExpression
     * @deprecated Use `or()` instead.
     *
     */
    public function orX($x = null)
    {
        return new CompositeExpression(CompositeExpression::TYPE_OR, func_get_args());
    }
}
