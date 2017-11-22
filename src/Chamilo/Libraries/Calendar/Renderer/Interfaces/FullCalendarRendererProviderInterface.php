<?php
namespace Chamilo\Libraries\Calendar\Renderer\Interfaces;

/**
 * An interface which forces the implementing Application to provide a given set of methods
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Interfaces
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
interface FullCalendarRendererProviderInterface
{

    public function getEventSources();

    /**
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getDataUser();

    /**
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getViewingUser();
}