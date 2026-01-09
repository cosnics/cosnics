<?php
namespace Chamilo\Libraries\Calendar\Architecture\Interfaces;

/**
 * @package Chamilo\Libraries\Calendar\Architecture\Interfaces
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface VisibilityServiceInterface
{
    public function changeVisibility(string $userIdentifier, string $source): bool;
}