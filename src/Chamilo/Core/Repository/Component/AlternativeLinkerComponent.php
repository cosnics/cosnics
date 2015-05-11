<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Interfaces\ApplicationSupport;
use Chamilo\Libraries\Platform\Session\Request;

/**
 * This component executes the ContentObjectAlternativeLinker Submanager
 *
 * @package repository
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AlternativeLinkerComponent extends Manager implements ApplicationSupport
{

    /**
     * Executes this component
     */
    public function run()
    {
        $factory = new ApplicationFactory(
            $this->getRequest(),
            \Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Alternative\Manager :: context(),
            $this->get_user(),
            $this);
        return $factory->run();
    }

    /**
     * Returns the selected content object id
     *
     * @return int
     */
    public function get_selected_content_object_id()
    {
        return Request :: get(self :: PARAM_CONTENT_OBJECT_ID);
    }
}
