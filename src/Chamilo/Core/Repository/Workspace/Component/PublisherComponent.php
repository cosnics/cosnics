<?php
namespace Chamilo\Core\Repository\Workspace\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRelationRepository;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceContentObjectRelation;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PublisherComponent extends Manager
{

    public function run()
    {
        if (! $this->getCurrentWorkspace() instanceof Workspace)
        {
            throw new \Exception(Translation :: get('NoValidWorkspace'));
        }

        if (! RightsService :: getInstance()->canAddContentObjects($this->get_user(), $this->getCurrentWorkspace()))
        {
            throw new NotAllowedException();
        }

        if (! \Chamilo\Core\Repository\Viewer\Manager :: is_ready_to_be_published())
        {
            $this->getRequest()->query->set(
                \Chamilo\Core\Repository\Viewer\Manager :: PARAM_ACTION,
                \Chamilo\Core\Repository\Viewer\Manager :: ACTION_BROWSER);

            $factory = new ApplicationFactory(
                \Chamilo\Core\Repository\Viewer\Manager :: context(),
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));

            $component = $factory->getComponent();
            $component->set_excluded_objects($this->getExcludedObjects());
            $component->set_actions(array(\Chamilo\Core\Repository\Viewer\Manager :: ACTION_BROWSER));
            return $component->run();
        }
        else
        {
            $selectedContentObjectIdentifiers = (array) \Chamilo\Core\Repository\Viewer\Manager :: get_selected_objects();

            foreach ($selectedContentObjectIdentifiers as $selectedContentObjectIdentifier)
            {
                $contentObjectRelationService = new ContentObjectRelationService(new ContentObjectRelationRepository());
                $contentObjectRelationService->createContentObjectRelation(
                    $this->getCurrentWorkspace()->getId(),
                    $selectedContentObjectIdentifier,
                    0);
            }

            $this->redirect(
                Translation :: get('ContentObjectsAddedToWorkspace'),
                false,
                array(
                    \Chamilo\Core\Repository\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Manager :: ACTION_BROWSE_CONTENT_OBJECTS,
                    self :: PARAM_ACTION => null));
        }
    }

    /**
     *
     * @return string[]
     */
    public function get_allowed_content_object_types()
    {
        $registrations = Configuration :: registrations_by_type('Chamilo\Core\Repository\ContentObject');

        foreach ($registrations as $registration)
        {
            $namespace = $registration[Registration :: PROPERTY_CONTEXT];
            $types[] = $namespace . '\Storage\DataClass\\' .
                 ClassnameUtilities :: getInstance()->getPackageNameFromNamespace($namespace);
        }

        return $types;
    }

    public function getExcludedObjects()
    {
        $workspace = $this->get_application()->getWorkspace();

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceContentObjectRelation :: class_name(),
                WorkspaceContentObjectRelation :: PROPERTY_WORKSPACE_ID),
            new StaticConditionVariable($workspace->getId()));

        return DataManager :: distinct(
            WorkspaceContentObjectRelation :: class_name(),
            new DataClassDistinctParameters($condition, WorkspaceContentObjectRelation :: PROPERTY_CONTENT_OBJECT_ID));
    }

    public function getCurrentWorkspace()
    {
        return $this->get_application()->getWorkspace();
    }
}
