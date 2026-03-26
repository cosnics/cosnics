<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Service\UserUrlGenerator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Protocol\Ajax\Architecture\Domain\JsonAjaxResult;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderProperty;
use Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Component
 * @author  Sven Vanpoucke
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UsersFeedComponent extends Manager
{
    public const string PARAM_OFFSET = 'offset';
    public const string PARAM_SEARCH_QUERY = 'query';
    public const string PROPERTY_ELEMENTS = 'elements';
    public const string PROPERTY_TOTAL_ELEMENTS = 'total_elements';

    private int $userCount = 0;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, UrlGenerator $urlGenerator,
        AuthenticationValidator $authenticationValidator, UserUrlGenerator $userUrlGenerator,
        MailerInterface $activeMailer, AlertsManager $alertsManager, UserService $userService,
        protected readonly SearchQueryConditionGenerator $searchQueryConditionGenerator
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $urlGenerator,
            $authenticationValidator, $userUrlGenerator, $activeMailer, $alertsManager, $userService
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function run(?User $currentUser = null): Response
    {
        $result = new JsonAjaxResult();

        $result->setProperty(self::PROPERTY_ELEMENTS, $this->getElements()->asArray());
        $result->setProperty(self::PROPERTY_TOTAL_ELEMENTS, $this->userCount);

        return $result->getResponse();
    }

    /**
     * @return \Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\AndCondition
     */
    protected function getCondition(): AndCondition
    {
        $searchQuery = $this->getRequest()->request->get(self::PARAM_SEARCH_QUERY);

        $conditions = [];

        // Set the conditions for the search query
        if ($searchQuery && $searchQuery != '') {
            $conditions[] = $this->searchQueryConditionGenerator->getSearchConditions(
                $searchQuery, [
                    new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME),
                    new PropertyConditionVariable(User::class, User::PROPERTY_GIVEN_NAME),
                    new PropertyConditionVariable(User::class, User::PROPERTY_SURNAME),
                    new PropertyConditionVariable(User::class, User::PROPERTY_OFFICIAL_CODE)
                ]
            );
        }

        // Only include active users
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_ACTIVE), new StaticConditionVariable(1)
        );

        return new AndCondition($conditions);
    }

    protected function getElementForUser(User $user): AdvancedElementFinderElement
    {
        $glyph = new FontAwesomeGlyph('user', [], null, 'fas');

        return new AdvancedElementFinderElement(
            'user_' . $user->getId(), $glyph->getClassNamesString(), $user->getFullName(), $user->getOfficialCode()
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Exception
     */
    protected function getElements(): AdvancedElementFinderElements
    {
        $translator = $this->getTranslator();
        $elements = new AdvancedElementFinderElements();

        $glyph = new FontAwesomeGlyph('folder', [], null, 'fas');

        // Add user category
        $userCategory = new AdvancedElementFinderElement(
            'users', $glyph->getClassNamesString(), $translator->trans('Users', [], Manager::CONTEXT),
            $translator->trans('Users', [], Manager::CONTEXT)
        );
        $elements->addElement($userCategory);

        foreach ($this->retrieveUsers() as $user) {
            $userCategory->addChild($this->getElementForUser($user));
        }

        return $elements;
    }

    protected function getOffset(): int
    {
        return $this->getRequest()->request->get(self::PARAM_OFFSET, 0);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function retrieveUsers(): ArrayCollection
    {
        $condition = $this->getCondition();

        $this->userCount = $this->userService->countUsers($condition);

        return $this->userService->findUsers(
            $condition, $this->getOffset(), 100, new OrderBy([
                new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_SURNAME)),
                new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_GIVEN_NAME)),
            ])
        );
    }
}
