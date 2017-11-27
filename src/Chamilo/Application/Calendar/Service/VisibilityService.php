<?php
namespace Chamilo\Application\Calendar\Service;

use Chamilo\Application\Calendar\Repository\VisibilityRepository;

/**
 *
 * @package Chamilo\Application\Calendar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class VisibilityService
{

    /**
     *
     * @var \Chamilo\Application\Calendar\Repository\VisibilityRepository
     */
    private $visibilityRepository;

    /**
     *
     * @param \Chamilo\Application\Calendar\Repository\VisibilityRepository $visibilityRepository
     */
    public function __construct(VisibilityRepository $visibilityRepository)
    {
        $this->visibilityRepository = $visibilityRepository;
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Repository\VisibilityRepository
     */
    public function getVisibilityRepository()
    {
        return $this->visibilityRepository;
    }

    /**
     *
     * @param \Chamilo\Application\Calendar\Repository\VisibilityRepository $visibilityRepository
     */
    public function setVisibilityRepository(VisibilityRepository $visibilityRepository)
    {
        $this->visibilityRepository = $visibilityRepository;
    }

    /**
     *
     * @param string $source
     * @param integer $userIdentifier
     * @return \Chamilo\Application\Calendar\Storage\DataClass\Visibility
     */
    public function findVisibilityBySourceAndUserIdentifier($source, $userIdentifier = null)
    {
        return $this->getVisibilityRepository()->findVisibilityBySourceAndUserIdentifier($source, $userIdentifier);
    }
}