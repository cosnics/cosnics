<?php
namespace Chamilo\Libraries\Architecture\Interface;

use Chamilo\Core\User\Storage\DataClass\User;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Libraries\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ApplicationInterface
{
    public function run(?User $currentUser = null): Response;

    public function getAction(): string;

    public function getContext(): string;

    public function getDefaultAction(): string;
}