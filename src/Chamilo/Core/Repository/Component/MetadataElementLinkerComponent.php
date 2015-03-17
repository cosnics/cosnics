<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Interfaces\ApplicationSupport;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 * This component executes the ContentObjectMetadataElementLinkerComponent Submanager
 *
 * @package repository
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class MetadataElementLinkerComponent extends Manager implements ApplicationSupport
{

    /**
     * Executes this component
     */
    public function run()
    {
        $factory = new ApplicationFactory(
            $this->getRequest(),
            \Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Type\Manager :: context(),
            $this->get_user(),
            $this);
        return $factory->run();
    }

    /**
     * Returns the breadcrumb generator
     *
     * @return BreadcrumbGeneratorInterface
     */
    public function get_breadcrumb_generator()
    {
        return new \Chamilo\Core\Admin\Core\BreadcrumbGenerator($this, BreadcrumbTrail :: get_instance());
    }

    /**
     * Determines that this component does not have a menu
     *
     * @return bool
     */
    public function has_menu()
    {
        return false;
    }
}
