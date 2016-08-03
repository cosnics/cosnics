<?php

namespace Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar\Repository;

use Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar\Interfaces\PersonalCalendarEventDataProviderRepositoryInterface;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication;
use Chamilo\Core\Repository\Publication\Storage\Repository\PublicationRepository;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Abstract class for the repository to provide publications for the personal calendar extension
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class PersonalCalendarEventDataProviderRepository implements PersonalCalendarEventDataProviderRepositoryInterface
{
    /**
     * @var PublicationRepository
     */
    protected $publicationRepository;

    /**
     * PersonalCalendarEventDataProviderRepository constructor.
     *
     * @param PublicationRepository $publicationRepository
     */
    public function __construct(PublicationRepository $publicationRepository)
    {
        $this->publicationRepository = $publicationRepository;
    }

    /**
     * Returns the personal calendar publications for this specific content object type
     *
     * @param RecordRetrievesParameters $parameters
     * @param int $fromDate
     * @param int $toDate
     *
     * @return Publication[]
     */
    public function getPublications(RecordRetrievesParameters $parameters, $fromDate, $toDate)
    {
        $parameters = clone $parameters;

        $condition = $this->getContentObjectCondition($fromDate, $toDate);

        if($condition)
        {
            $baseCondition = $parameters->get_condition();

            if ($baseCondition instanceof Condition)
            {
                $parameters->set_condition(new AndCondition(array($baseCondition, $condition)));
            }
            else
            {
                $parameters->set_condition($condition);
            }
        }

        return $this->publicationRepository->getPublicationsWithContentObjects(
            $parameters, Publication::class_name(), $this->getContentObjectClassName()
        );
    }

    /**
     * Returns the class name for the content object to be joined with
     *
     * @return string
     */
    abstract protected function getContentObjectClassName();

    /**
     * Returns the condition for the content object
     *
     * @param int $fromDate
     * @param int $toDate
     *
     * @return Condition
     */
    abstract protected function getContentObjectCondition($fromDate, $toDate);

}