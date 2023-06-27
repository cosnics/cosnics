<?php
namespace Chamilo\Core\User\Architecture\Interfaces;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;

/**
 * @package Chamilo\Core\User\Architecture\Interfaces
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface UserDetailsRendererInterface
{
    public function getGlyph(): InlineGlyph;

    public function renderTitle(User $user, User $requestingUser): string;

    public function renderUserDetails(User $user, User $requestingUser): string;

    public function renderUserDetailsForUserIdentifier(string $userIdentifier, User $requestingUser): string;

    public function hasContentForUser(User $user, User $requestingUser): bool;
}