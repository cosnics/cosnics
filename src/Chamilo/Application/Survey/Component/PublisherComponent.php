<?php
namespace Chamilo\Application\Survey\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Application\Survey\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Application\Survey\Storage\DataClass\PublicationContentObjectRelation;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Application\Survey\Service\ContentObjectRelationService;
use Chamilo\Application\Survey\Repository\ContentObjectRelationRepository;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Application\Survey\Service\RightsService;

/**
 *
 * @package Chamilo\Application\Survey\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PublisherComponent extends Manager
{

    public function run()
    {
        if (! $this->getCurrentPublication() instanceof Publication)
        {
            throw new \Exception(Translation :: get('NoValidPublication'));
        }

        if (! RightsService :: getInstance()->canAddContentObjects($this->get_user(), $this->getCurrentPublication()))
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
                    $this->getCurrentPublication()->getId(),
                    $selectedContentObjectIdentifier,
                    0);
            }

            $this->redirect(
                Translation :: get('ContentObjectsAddedToPublication'),
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
            $namespace = $registration->get_context();
            $types[] = $namespace . '\Storage\DataClass\\' .
                 ClassnameUtilities :: getInstance()->getPackageNameFromNamespace($namespace);
        }

        return $types;
    }

    public function getExcludedObjects()
    {
        $publication = $this->get_application()->getPublication();

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                PublicationContentObjectRelation :: class_name(),
                PublicationContentObjectRelation :: PROPERTY_PUBLICATION_ID),
            new StaticConditionVariable($publication->getId()));

        return DataManager :: distinct(
            PublicationContentObjectRelation :: class_name(),
            new DataClassDistinctParameters($condition, PublicationContentObjectRelation :: PROPERTY_CONTENT_OBJECT_ID));
    }

    public function getCurrentPublication()
    {
        return $this->get_application()->getPublication();
    }
}
