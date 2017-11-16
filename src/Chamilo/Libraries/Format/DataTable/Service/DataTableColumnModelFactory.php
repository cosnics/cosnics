<?php
namespace Chamilo\Libraries\Format\DataTable\Service;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Libraries\Format\DataTable\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 */
class DataTableColumnModelFactory
{

    /**
     *
     * @param \Chamilo\Libraries\Format\DataTable\Service\DataTableProvider $dataTableProvider
     *
     * @return \Chamilo\Libraries\Format\DataTable\DataTableColumnModel
     */
    public function getDataTableColumnModel(DataTableProvider $dataTableProvider)
    {
        $dataTableProviderContext = ClassnameUtilities::getInstance()->getNamespaceFromObject($dataTableProvider);
        $dataTableProviderClassName = ClassnameUtilities::getInstance()->getClassnameFromObject($dataTableProvider);

        $dataTableProviderType = StringUtilities::getInstance()->createString($dataTableProviderClassName)
            ->replace('DataTableProvider', '')
            ->__toString();

        $className = $dataTableProviderContext . '\\' . $dataTableProviderType . 'DataTableColumnModel';

        return new $className();
    }
}

