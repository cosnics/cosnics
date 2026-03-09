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
    public const PARAM_ACTION = 'action';
    public const PARAM_CONTEXT = 'context';

    public function run(?User $currentUser = null): Response;

    public function getApplicationAction(): string;

    public function getApplicationContext(): string;

    public function getDefaultApplicationAction(): string;
}