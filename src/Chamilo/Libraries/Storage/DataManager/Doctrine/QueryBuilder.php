<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine;

use Exception;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class QueryBuilder extends \Doctrine\DBAL\Query\QueryBuilder
{

    /**
     *
     * @var string
     */
    private $sql;

    /**
     *
     * @var integer
     */
    private $state;

    /**
     *
     * @return string
     */
    private function getSQLForDelete()
    {
        $sqlParts = $this->getQueryParts();
        $table = $sqlParts['from']['table'] . ($sqlParts['from']['alias'] ? ' ' . $sqlParts['from']['alias'] : '');
        $query = 'DELETE' . ($sqlParts['from']['alias'] ? ' ' . $sqlParts['from']['alias'] : '') . ' FROM ' . $table .
             ($sqlParts['where'] !== null ? ' WHERE ' . ((string) $sqlParts['where']) : '');

        return $query;
    }

    /**
     *
     * @see \Doctrine\DBAL\Query\QueryBuilder::getSQL()
     */
    public function getSQL()
    {
        if ($this->getType() != self::DELETE)
        {
            throw new Exception();
        }

        if ($this->sql !== null && $this->state === self::STATE_CLEAN)
        {
            return $this->sql;
        }

        $sql = $this->getSQLForDelete();

        $this->state = self::STATE_CLEAN;
        $this->sql = $sql;

        return $sql;
    }
}