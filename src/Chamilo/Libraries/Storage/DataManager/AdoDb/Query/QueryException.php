<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\Query;

/**
 * Based on the Doctrine DBAL Query-builder architecture
 *
 * @link www.doctrine-project.org
 * @since 2.1.4
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @license MIT
 */
class QueryException extends \Exception
{

    /**
     *
     * @param string $alias
     * @param array $registeredAliases
     *
     * @return \Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryException
     */
    static public function unknownAlias($alias, $registeredAliases)
    {
        return new self(
            "The given alias '" . $alias . "' is not part of " .
                 "any FROM or JOIN clause table. The currently registered " . "aliases are: " .
                 implode(", ", $registeredAliases) . ".");
    }

    /**
     *
     * @param string $alias
     * @param array $registeredAliases
     *
     * @return \Chamilo\Libraries\Storage\DataManager\AdoDb\Query\QueryException
     */
    static public function nonUniqueAlias($alias, $registeredAliases)
    {
        return new self(
            "The given alias '" . $alias . "' is not unique " .
                 "in FROM and JOIN clause table. The currently registered " . "aliases are: " .
                 implode(", ", $registeredAliases) . ".");
    }
}
