<?php
namespace Chamilo\Libraries\Calendar\Architecture\Interface;

/**
 * @package Chamilo\Libraries\Calendar\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface VisibilityServiceInterface
{
    public function changeVisibility(string $userIdentifier, string $source): void;
}