<?php
namespace Chamilo\Libraries\Architecture\Application;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\ChamiloRequest;

/**
 *
 * @package Chamilo\Libraries\Architecture\Application
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
interface ApplicationConfigurationInterface
{

    public function get(string $key, ?string $defaultValue = null): string;

    public function getApplication(): ?Application;

    public function getRequest(): ChamiloRequest;

    public function getUser(): ?User;

    public function set(string $key, string $value): ApplicationConfigurationInterface;

    public function setUser(?User $user): ApplicationConfigurationInterface;
}
