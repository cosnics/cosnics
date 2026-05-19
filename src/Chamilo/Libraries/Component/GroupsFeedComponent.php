<?php
namespace Chamilo\Libraries\Component;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Manager;
use Chamilo\Libraries\Protocol\Ajax\Architecture\Domain\JsonAjaxResult;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderProperty;
use Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

/**
 * @author  Sven Vanpoucke
 * @package Chamilo\Libraries\Component
 */
abstract class GroupsFeedComponent extends Manager
{
    public const string PARAM_FILTER = 'filter';
    public const string PARAM_OFFSET = 'offset';
    public const string PARAM_SEARCH_QUERY = 'query';
    public const string PROPERTY_ELEMENTS = 'elements';
    public const string PROPERTY_TOTAL_ELEMENTS = 'total_elements';

    protected int $userCount = 0;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, UrlGenerator $urlGenerator,
        protected UserService $userService, protected SearchQueryConditionGenerator $searchQueryConditionGenerator
    )
    {
        parent::__construct($request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $urlGenerator);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function run(?User $currentUser = null): Response
    {
        if (!$currentUser instanceof User) {
            throw new NotAllowedException();
        }

        $result = new JsonAjaxResult();

        $elements = $this->getElements();
        $elements = $elements->asArray();

        $result->setProperty(self::PROPERTY_ELEMENTS, $elements);

        if ($this->userCount > 0) {
            $result->setProperty(self::PROPERTY_TOTAL_ELEMENTS, $this->userCount);
        }

        return $result->getResponse();
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Exception
     */
    private function getElements(): AdvancedElementFinderElements
    {
        $elements = new AdvancedElementFinderElements();
        $glyph = new FontAwesomeGlyph('folder', [], null, 'fas');

        // Add groups
        $groups = $this->retrieveGroups();
        if ($groups->count() > 0) {
            $translator = $this->getTranslator();
            // Add group category
            $groupCategory = new AdvancedElementFinderElement(
                'groups', $glyph->getClassNamesString(), $translator->trans('Groups', [], StringUtilities::LIBRARIES),
                $translator->trans('Groups', [], StringUtilities::LIBRARIES)
            );
            $elements->addElement($groupCategory);

            foreach ($groups as $group) {
                $groupCategory->addChild($this->getGroupElement($group));
            }
        }

        // Add users
        $users = $this->retrieveUsers();
        if ($users->count() > 0) {
            // Add user category
            $userCategory = new AdvancedElementFinderElement('users', $glyph->getClassNamesString(), 'Users', 'Users');
            $elements->addElement($userCategory);

            foreach ($users as $user) {
                $userCategory->addChild($this->getUserElement($user));
            }
        }

        return $elements;
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return \Chamilo\Libraries\UserInterface\Form\Architecture\Domain\AdvancedElementFinder\AdvancedElementFinderElement
     */
    abstract public function getGroupElement(Group $group): AdvancedElementFinderElement;

    protected function getOffset(): int
    {
        $offset = $this->getRequest()->request->get(self::PARAM_OFFSET);
        if (!isset($offset)) {
            $offset = 0;
        }

        return $offset;
    }

    abstract public function getUserElement(User $user): AdvancedElementFinderElement;

    /**
     * @return int[]
     */
    abstract public function getUserIdentifiers(): array;

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     */
    abstract public function retrieveGroups(): ArrayCollection;

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\Entity\User>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    private function retrieveUsers(): ArrayCollection
    {
        $conditions = [];

        $userIdentifiers = $this->getUserIdentifiers();

        if (count($userIdentifiers) == 0) {
            return new ArrayCollection();
        }

        $conditions[] =
            new InCondition(new PropertyConditionVariable(User::class, DataClass::PROPERTY_ID), $userIdentifiers);

        $searchQuery = $this->getRequest()->request->get(self::PARAM_SEARCH_QUERY);

        // Set the conditions for the search query
        if ($searchQuery && $searchQuery != '') {
            $conditions[] = $this->searchQueryConditionGenerator->getSearchConditions(
                $searchQuery, [
                    new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME),
                    new PropertyConditionVariable(User::class, User::PROPERTY_GIVEN_NAME),
                    new PropertyConditionVariable(User::class, User::PROPERTY_SURNAME)
                ]
            );
        }

        $condition = new AndCondition($conditions);

        $this->userCount = $this->userService->countUsers($condition);

        return $this->userService->findUsers(
            $condition, $this->getOffset(), 100, new OrderBy([
                new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_SURNAME)),
                new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_GIVEN_NAME))
            ])
        );
    }
}
