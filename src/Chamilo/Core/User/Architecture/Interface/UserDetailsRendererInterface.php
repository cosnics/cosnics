<?php
namespace Chamilo\Core\User\Architecture\Interface;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;

/**
 * @package Chamilo\Core\User\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface UserDetailsRendererInterface
{
    public function getGlyph(): InlineGlyph;

    public function hasContentForUser(User $user, User $requestingUser): bool;

    public function renderTitle(User $user, User $requestingUser): string;

    public function renderUserDetails(User $user, User $requestingUser): string;

    public function renderUserDetailsForUserIdentifier(string $userIdentifier, User $requestingUser): string;
}