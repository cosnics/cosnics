<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Component;

use Chamilo\Application\Calendar\Extension\Personal\Manager;
use Chamilo\Application\Calendar\Extension\Personal\Publisher\Publisher;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;

/**
 *
 * @package application\calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublisherComponent extends Manager implements \Chamilo\Core\Repository\Viewer\ViewerInterface, DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! \Chamilo\Core\Repository\Viewer\Manager :: is_ready_to_be_published())
        {
            $factory = new ApplicationFactory(
                \Chamilo\Core\Repository\Viewer\Manager :: context(),
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
            return $factory->run();
        }
        else
        {
            $publisher = new Publisher($this);
            return $publisher->get_publications_form(\Chamilo\Core\Repository\Viewer\Manager :: get_selected_objects());
        }
    }

    /**
     *
     * @see \libraries\architecture\application\Application::add_additional_breadcrumbs()
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('personal_calendar_publisher');
    }

    /**
     *
     * @return string[]
     */
    public function get_allowed_content_object_types()
    {
        $registrations = \Chamilo\Configuration\Storage\DataManager :: get_integrating_contexts(
            Manager :: package(),
            \Chamilo\Core\Repository\Manager :: context() . '\ContentObject');
        $types = array();

        foreach ($registrations as $registration)
        {
            $namespace = ClassnameUtilities :: getInstance()->getNamespaceParent($registration->get_context(), 6);
            $types[] = $namespace . '\Storage\DataClass\\' .
                 ClassnameUtilities :: getInstance()->getPackageNameFromNamespace($namespace);
        }

        return $types;
    }
}
