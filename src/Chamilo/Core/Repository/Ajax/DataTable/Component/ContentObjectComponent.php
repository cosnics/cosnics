<?php
namespace Chamilo\Core\Repository\Ajax\DataTable\Component;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Libraries\Architecture\JsonDataClassTableResponse;
use Chamilo\Libraries\Format\DataTable\Interfaces\DataTablePagedComponentInterface;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 *
 * @package Chamilo\Core\Repository\Ajax\DataTable\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 */
class ContentObjectComponent extends \Chamilo\Core\Repository\Ajax\Manager implements DataTablePagedComponentInterface
{
    use \Chamilo\Libraries\Format\DataTable\Traits\DataTablePagedComponentTrait;

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable[]
     */
    public function getGlobalFilterProperties()
    {
        return array(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TITLE),
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION));
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\PersonalWorkspace
     */
    protected function getWorkspaceImplementation()
    {
        return new PersonalWorkspace($this->getUser());
    }

    public function run()
    {
        $tableDataProvider = $this->getDataTableProvider();

        $jsonResponse = new JsonDataClassTableResponse(
            $tableDataProvider->getDataTableRowData(
                $this->getDataClassRetrievesParameters(),
                $this->getWorkspaceImplementation()),
            $tableDataProvider->getDataTableRowCount(
                $this->getDataClassRetrievesParameters(),
                $this->getWorkspaceImplementation()));
        $jsonResponse->send();
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Ajax\DataTable\Type\ContentObject\ContentObjectDataTableProvider
     */
    public function getDataTableProvider()
    {
        return $this->getService(
            'chamilo.core.repository.ajax.data_table.type.content_object.content_object_data_table_provider');
    }
}
