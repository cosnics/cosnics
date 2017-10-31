<?php
namespace Chamilo\Core\Repository\Ajax\DataTable\Component;

use Chamilo\Core\Repository\Ajax\Tables\Service\ContentObjectDataTableProvider;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
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
     * @return \Chamilo\Core\Repository\Workspace\Service\ContentObjectService
     */
    protected function getContentObjectService()
    {
        return $this->getService('chamilo.core.repository.workspace.service.content_object_service');
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\PersonalWorkspace
     */
    protected function getWorkspaceImplementation()
    {
        return new PersonalWorkspace($this->getUser());
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\DataTable\Interfaces\DataTablePagedComponentInterface::getDataTableProvider()
     */
    public function getDataTableProvider()
    {
        return new ContentObjectDataTableProvider(
            $this->getDataClassRetrievesParameters(),
            $this->getContentObjectService(),
            $this->getWorkspaceImplementation());
    }
}
