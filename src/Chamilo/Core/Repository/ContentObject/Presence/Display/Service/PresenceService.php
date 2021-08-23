<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Service;

use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Core\Repository\ContentObject\Presence\Display\Storage\Repository\PresenceRepository;

/**
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class PresenceService
{
    /**
     * @var PresenceRepository
     */
    protected $presenceRepository;

    public function __construct(PresenceRepository $presenceRepository)
    {
        $this->presenceRepository = $presenceRepository;
    }

    /**
     * @param int $presenceId
     * @param ContextIdentifier $contextIdentifier
     */
    public function getResultPeriodsForPresence(int $presenceId, ContextIdentifier $contextIdentifier)
    {
        $periods = $this->presenceRepository->getResultPeriodsForPresence($presenceId, $contextIdentifier);
        $periods = iterator_to_array($periods);
        foreach ($periods as $index => $period)
        {
            $periods[$index]['date'] = (int) $period['date'];
            $periods[$index]['id'] = (int) $period['id'];
        }
        return $periods;
    }
}