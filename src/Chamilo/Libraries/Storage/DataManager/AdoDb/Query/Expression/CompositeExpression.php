<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\Query\Expression;

use Countable;
use function array_merge;
use function count;
use function implode;

/**
 * Based on the Doctrine DBAL Query-builder architecture
 * Composite expression is responsible to build a group of similar expression.
 *
 * @link www.doctrine-project.org
 * @version 3.3.6
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @license MIT
 */
class CompositeExpression implements Countable
{
    /**
     * Constant that represents an AND composite expression.
     */
    public const TYPE_AND = 'AND';

    /**
     * Constant that represents an OR composite expression.
     */
    public const TYPE_OR = 'OR';

    /**
     * Each expression part of the composite expression.
     *
     * @var self[]|string[]
     */
    private $parts = [];

    /**
     * The instance type of composite expression.
     *
     * @var string
     */
    private $type;

    /**
     * @param string $type Instance type of composite expression.
     * @param self[]|string[] $parts Composition of expressions to be joined on composite expression.
     *
     * @internal Use the and() / or() factory methods.
     *
     */
    public function __construct($type, array $parts = [])
    {
        $this->type = $type;

        $this->addMultiple($parts);
    }

    /**
     * Retrieves the string representation of this composite expression.
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->count() === 1)
        {
            return (string) $this->parts[0];
        }

        return '(' . implode(') ' . $this->type . ' (', $this->parts) . ')';
    }

    /**
     * Adds an expression to composite expression.
     *
     * @param mixed $part
     *
     * @return CompositeExpression
     * @deprecated This class will be made immutable. Use with() instead.
     *
     */
    public function add($part)
    {
        if ($part === null)
        {
            return $this;
        }

        if ($part instanceof self && count($part) === 0)
        {
            return $this;
        }

        $this->parts[] = $part;

        return $this;
    }

    /**
     * Adds multiple parts to composite expression.
     *
     * @param self[]|string[] $parts
     *
     * @return CompositeExpression
     * @deprecated This class will be made immutable. Use with() instead.
     *
     */
    public function addMultiple(array $parts = [])
    {
        foreach ($parts as $part)
        {
            $this->add($part);
        }

        return $this;
    }

    /**
     * @param self|string $part
     * @param self|string ...$parts
     */
    public static function and($part, ...$parts): self
    {
        return new self(self::TYPE_AND, array_merge([$part], $parts));
    }

    /**
     * Retrieves the amount of expressions on composite expression.
     *
     * @return int
     */
    public function count()
    {
        return count($this->parts);
    }

    /**
     * Returns the type of this composite expression (AND/OR).
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param self|string $part
     * @param self|string ...$parts
     */
    public static function or($part, ...$parts): self
    {
        return new self(self::TYPE_OR, array_merge([$part], $parts));
    }

    /**
     * Returns a new CompositeExpression with the given parts added.
     *
     * @param self|string $part
     * @param self|string ...$parts
     */
    public function with($part, ...$parts): self
    {
        $that = clone $this;

        $that->parts = array_merge($that->parts, [$part], $parts);

        return $that;
    }
}
