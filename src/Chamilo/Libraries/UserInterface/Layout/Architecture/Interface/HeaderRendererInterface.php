<?php
namespace Chamilo\Libraries\UserInterface\Layout\Architecture\Interface;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
interface HeaderRendererInterface
{

    public function render(?User $user = null): string;
}