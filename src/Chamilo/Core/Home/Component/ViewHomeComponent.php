<?php
namespace Chamilo\Core\Home\Component;

use Chamilo\Core\Group\Storage\Entity\Group;
use Chamilo\Core\Group\Storage\Repository\GroupEntityRepository;
use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\UserInterface\HomeRenderer\HomeRenderer;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\NoAuthenticationSupportInterface;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Uid\UuidV7;

/**
 * @package Chamilo\Core\Home\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ViewHomeComponent extends Manager implements NoAuthenticationSupportInterface
{
    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, UrlGenerator $urlGenerator,
        protected readonly AuthenticationValidator $authenticationValidator,
        protected readonly HomeRenderer $homeRenderer, protected readonly GroupEntityRepository $groupEntityRepository
    )
    {
        parent::__construct($request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $urlGenerator);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAuthenticatedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function run(?User $currentUser = null): Response
    {
        /**
         * TODO: Rights
         * - Platform admin
         * - Selected user(s)
         * - Selected group(s)
         * - Selected Entra group(s)
         * -> Via IDM or Graph API?
         * -> Mapping of usernames / user principals
         */
        $this->authenticationValidator->validate();

        $parent = new Group();
        $parent->setIdentifier(new UuidV7());
        $parent->setName('Test Title');
        $parent->setDescription('Test description');
        $parent->setCode('TEST');

        $group = new Group();
        $group->setIdentifier(new UuidV7());
        $group->setName('Child Test');
        $group->setDescription('Child Test description');
        $group->setCode('CHILD');
        $group->setParent($parent);

        $child = new Group();
        $child->setIdentifier(new UuidV7());
        $child->setName('Child Test 1');
        $child->setDescription('Child Test description 1');
        $child->setCode('CHILD1');
        $child->setParent($group);

        $anotherChild = new Group();
        $anotherChild->setIdentifier(new UuidV7());
        $anotherChild->setName('Child Test 2');
        $anotherChild->setDescription('Child Test description 2');
        $anotherChild->setCode('CHILD2');
        $anotherChild->setParent($group);

        $anotherGroup = new Group();
        $anotherGroup->setIdentifier(new UuidV7());
        $anotherGroup->setName('Child Test');
        $anotherGroup->setDescription('Child Test description');
        $anotherGroup->setCode('GRPU');
        $anotherGroup->setParent($parent);

        $this->groupEntityRepository->saveGroup($parent);
        $this->groupEntityRepository->saveGroup($group);
        $this->groupEntityRepository->saveGroup($child);
        $this->groupEntityRepository->saveGroup($anotherChild);
        $this->groupEntityRepository->saveGroup($anotherGroup);

        $html = [];

        $html[] = $this->renderHeader($currentUser);
        $html[] = $this->homeRenderer->render(null, $currentUser);
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }
}
