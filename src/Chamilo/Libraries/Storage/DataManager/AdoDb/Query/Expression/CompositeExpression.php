<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\Query\Expression;

use Countable;

/**
 * Composite expression is responsible to build a group of similar expression.
 * Based on the Doctrine DBAL Query-builder architecture
 *
 * @link www.doctrine-project.org
 * @since 2.1
 * @author Guilherme Blanco <guilhermeblanco@hotmail.com>
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @license MIT
 */
class CompositeExpression implements Countable
{
    /**
     * Constant that represents an AND composite expression.
     */
    const TYPE_AND = 'AND';

    /**
     * Constant that represents an OR composite expression.
     */
    const TYPE_OR = 'OR';

    /**
     * The instance type of composite expression.
     *
     * @var string
     */
    private $type;

    /**
     * Each expression part of the composite expression.
     *
     * @var array
     */
    private $parts = array();

    /**
     *
     * @param string $type Instance type of composite expression.
     * @param array $parts Composition of expressions to be joined on composite expression.
     */
    public function __construct($type, array $parts = array())
    {
        $this->type = $type;

        $this->addMultiple($parts);
    }

    /**
     *
     * @return string
     */
    public function __toString()
    {
        if (count($this->parts) === 1)
        {
            return (string) $this->parts[0];
        }

        return '(' . implode(') ' . $this->type . ' (', $this->parts) . ')';
    }

    /**
     *
     * @param mixed $part
     *
     * @return \Chamilo\Libraries\Storage\DataManager\AdoDb\Query\Expression\CompositeExpression
     */
    public function add($part)
    {
        if (!empty($part) || ($part instanceof self && $part->count() > 0))
        {
            $this->parts[] = $part;
        }

        return $this;
    }

    /**
     *
     * @param array $parts
     *
     * @return \Chamilo\Libraries\Storage\DataManager\AdoDb\Query\Expression\CompositeExpression
     */
    public function addMultiple(array $parts = array())
    {
        foreach ((array) $parts as $part)
        {
            $this->add($part);
        }

        return $this;
    }

    /**
     *
     * @return integer
     */
    public function count()
    {
        return count($this->parts);
    }

    /**
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
