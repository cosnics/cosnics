<?php
namespace Chamilo\Application\Calendar\Architecture\Interface;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Application\Calendar
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
interface CalendarExtensionActionProviderInterface
{
    /**
     * @return \Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonInterface[]
     */
    public function getAdditional(User $user): array;

    /**
     * @return \Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonInterface[]
     */
    public function getPrimary(User $user): array;
}