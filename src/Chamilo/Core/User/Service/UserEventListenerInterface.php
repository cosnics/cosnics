<?php
namespace Chamilo\Core\User\Service;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\User\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface UserEventListenerInterface
{
    public function afterCreate(User $user): bool;

    public function afterDelete(User $user): bool;

    public function afterUpdate(User $user): bool;

    public function beforeDelete(User $user): bool;
}