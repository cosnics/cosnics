<?php
namespace Chamilo\Libraries\Calendar\Renderer\Service;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class FullCalendarRendererProvider implements
    \Chamilo\Libraries\Calendar\Renderer\Interfaces\FullCalendarRendererProviderInterface
{

    /**
     *
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    private $dataUser;

    /**
     *
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    private $viewingUser;

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $dataUser
     * @param \Chamilo\Core\User\Storage\DataClass\User $viewingUser
     */
    public function __construct(User $dataUser, User $viewingUser)
    {
        $this->dataUser = $dataUser;
        $this->viewingUser = $viewingUser;
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface::getDataUser()
     */
    public function getDataUser()
    {
        return $this->dataUser;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $dataUser
     */
    public function setDataUser(User $dataUser)
    {
        $this->dataUser = $dataUser;
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface::getViewingUser()
     */
    public function getViewingUser()
    {
        return $this->viewingUser;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $viewingUser
     */
    public function setViewingUser(User $viewingUser)
    {
        $this->viewingUser = $viewingUser;
    }

    abstract public function getEventSources();
}