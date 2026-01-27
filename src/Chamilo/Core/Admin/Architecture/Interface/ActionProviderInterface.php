<?php
namespace Chamilo\Core\Admin\Architecture\Interface;

use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\Actions;

/**
 * @package Chamilo\Core\Admin\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ActionProviderInterface
{

    public function getActions(): Actions;

    public function getContext(): string;
}