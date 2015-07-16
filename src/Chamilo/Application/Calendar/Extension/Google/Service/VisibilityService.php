<?php
namespace Chamilo\Application\Calendar\Extension\Google\Service;

use Chamilo\Application\Calendar\Extension\Google\Repository\VisibilityRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Application\Calendar\Extension\Google\Storage\DataClass\Visibility;
use Chamilo\Libraries\Architecture\ActionResult;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Google\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class VisibilityService
{
    const PROPERTY_VISIBLE = 'visible';

    /**
     *
     * @var \Chamilo\Application\Calendar\Extension\Google\Repository\VisibilityRepository
     */
    private $visibilityRepository;

    /**
     *
     * @param \Chamilo\Application\Calendar\Extension\Google\Repository\VisibilityRepository $visibilityRepository
     */
    public function __construct(VisibilityRepository $visibilityRepository)
    {
        $this->visibilityRepository = $visibilityRepository;
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Extension\Google\Repository\VisibilityRepository
     */
    public function getVisibilityRepository()
    {
        return $this->visibilityRepository;
    }

    /**
     *
     * @param \Chamilo\Application\Calendar\Extension\Google\Repository\VisibilityRepository $visibilityRepository
     */
    public function setVisibilityRepository(VisibilityRepository $visibilityRepository)
    {
        $this->visibilityRepository = $visibilityRepository;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param boolean $isVisible
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function getVisibilitiesForUser(User $user, $isVisible = null)
    {
        return $this->getVisibilityRepository()->findVisibilitiesForUser($user, $isVisible);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function getActiveVisibilitiesForUser(User $user)
    {
        return $this->getVisibilitiesForUser($user, true);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function getInactiveVisibilitiesForUser(User $user)
    {
        return $this->getVisibilitiesForUser($user, false);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarIdentifier
     * @param boolean $isVisible
     * @return \Chamilo\Application\Calendar\Extension\Google\Storage\DataClass\Visibility
     */
    public function createVisibility(User $user, $calendarIdentifier, $isVisible = true)
    {
        $visibility = new Visibility();
        $this->setVisibilityProperties($visibility, $user, $calendarIdentifier, $isVisible);

        if (! $visibility->create())
        {
            return false;
        }

        return $visibility;
    }

    /**
     *
     * @param \Chamilo\Application\Calendar\Extension\Google\Storage\DataClass\Visibility $visibility
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarIdentifier
     * @param boolean $isVisible
     * @return \Chamilo\Application\Calendar\Extension\Google\Storage\DataClass\Visibility
     */
    public function updateVisibility(Visibility $visibility, User $user, $calendarIdentifier, $isVisible = true)
    {
        $this->setVisibilityProperties($visibility, $user, $calendarIdentifier, $isVisible);

        if (! $visibility->update())
        {
            return false;
        }

        return $visibility;
    }

    /**
     *
     * @param \Chamilo\Application\Calendar\Extension\Google\Storage\DataClass\Visibility $visibility
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarIdentifier
     * @param boolean $isVisible
     */
    private function setVisibilityProperties(Visibility $visibility, User $user, $calendarIdentifier, $isVisible)
    {
        $visibility->setUserId($user->getId());
        $visibility->setCalendarId($calendarIdentifier);
        $visibility->setVisibility($isVisible);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarIdentifier
     * @param boolean $isVisible
     * @return \Chamilo\Application\Calendar\Extension\Google\Storage\DataClass\Visibility
     */
    public function setVisibility(User $user, $calendarIdentifier, $isVisible = true)
    {
        $visibility = $this->getVisibilityByUserAndCalendarIdentifier($user, $calendarIdentifier);

        if ($visibility instanceof Visibility)
        {
            return $this->updateVisibility($visibility, $user, $calendarIdentifier, $isVisible);
        }
        else
        {
            return $this->createVisibility($user, $calendarIdentifier, $isVisible);
        }
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer[] $calendarVisibilities
     * @return \Chamilo\Libraries\Architecture\ActionResult
     */
    public function setVisibilities(User $user, $calendarVisibilities = array())
    {
        $failedActions = 0;

        foreach ($calendarVisibilities as $calendarIdentifier => $isVisible)
        {
            if (! $this->setVisibility($user, $calendarIdentifier, (boolean) $isVisible))
            {
                $failedActions ++;
            }
        }

        return new ActionResult(
            count($calendarVisibilities),
            $failedActions,
            __METHOD__,
            Visibility :: class_name(false));
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarIdentifier
     * @return \Chamilo\Application\Calendar\Extension\Google\Storage\DataClass\Visibility
     */
    public function getVisibilityByUserAndCalendarIdentifier(User $user, $calendarIdentifier)
    {
        return $this->getVisibilityRepository()->findVisibilityByUserAndCalendarIdentifier($user, $calendarIdentifier);
    }
}