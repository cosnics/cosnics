<?php
namespace Chamilo\Configuration\Service\UserDetails;

use Chamilo\Configuration\Form\Viewer;
use Chamilo\Core\User\Architecture\Interfaces\UserDetailsRendererInterface;
use Chamilo\Core\User\Architecture\Traits\UserDetailsRendererTrait;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Configuration\Service\UserDetails
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserDetailsRenderer implements UserDetailsRendererInterface
{
    use UserDetailsRendererTrait;

    protected Viewer $viewer;

    public function __construct(Translator $translator, Viewer $viewer)
    {
        $this->translator = $translator;
        $this->viewer = $viewer;
    }

    public function getGlyph(): InlineGlyph
    {
        return new NamespaceIdentGlyph('Chamilo\Configuration', true);
    }

    protected function getViewer(): Viewer
    {
        return $this->viewer;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function hasContentForUser(User $user, User $requestingUser): bool
    {
        return $this->getViewer()->getFormValues(Manager::CONTEXT, 'account_fields', $user->getId())->count() > 0;
    }

    public function renderTitle(User $user, User $requestingUser): string
    {
        return $this->getTranslator()->trans('AdditionalUserInformation', [], Manager::CONTEXT);
    }

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function renderUserDetails(User $user, User $requestingUser): string
    {
        return $this->getViewer()->render(Manager::CONTEXT, 'account_fields', $user->getId());
    }
}