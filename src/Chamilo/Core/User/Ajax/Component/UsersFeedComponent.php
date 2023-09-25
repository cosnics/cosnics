<?php
namespace Chamilo\Core\User\Ajax\Component;

use Chamilo\Core\User\Ajax\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\User\Ajax
 * @author  Sven Vanpoucke
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UsersFeedComponent extends Manager
{
    public const PARAM_OFFSET = 'offset';
    public const PARAM_SEARCH_QUERY = 'query';

    public const PROPERTY_ELEMENTS = 'elements';
    public const PROPERTY_TOTAL_ELEMENTS = 'total_elements';

    private int $userCount = 0;

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function run()
    {
        $result = new JsonAjaxResult();

        $result->set_property(self::PROPERTY_ELEMENTS, $this->getElements()->as_array());
        $result->set_property(self::PROPERTY_TOTAL_ELEMENTS, $this->userCount);

        $result->display();
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    protected function getCondition(): AndCondition
    {
        $searchQuery = $this->getRequest()->request->get(self::PARAM_SEARCH_QUERY);

        $conditions = [];

        // Set the conditions for the search query
        if ($searchQuery && $searchQuery != '')
        {
            $conditions[] = $this->getSearchQueryConditionGenerator()->getSearchConditions(
                $searchQuery, [
                    new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME),
                    new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME),
                    new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME),
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
            'user_' . $user->getId(), $glyph->getClassNamesString(), $user->get_fullname(), $user->get_official_code()
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function getElements(): AdvancedElementFinderElements
    {
        $translator = $this->getTranslator();
        $elements = new AdvancedElementFinderElements();

        $glyph = new FontAwesomeGlyph('folder', [], null, 'fas');

        // Add user category
        $user_category = new AdvancedElementFinderElement(
            'users', $glyph->getClassNamesString(), $translator->trans('Users', [], Manager::CONTEXT),
            $translator->trans('Users', [], Manager::CONTEXT)
        );
        $elements->add_element($user_category);

        foreach ($this->retrieveUsers() as $user)
        {
            $user_category->add_child($this->getElementForUser($user));
        }

        return $elements;
    }

    protected function getOffset(): int
    {
        return $this->getRequest()->request->get(self::PARAM_OFFSET, 0);
    }

    protected function getSearchQueryConditionGenerator(): SearchQueryConditionGenerator
    {
        return $this->getService(SearchQueryConditionGenerator::class);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function retrieveUsers(): ArrayCollection
    {
        $condition = $this->getCondition();

        $this->userCount = $this->getUserService()->countUsers($condition);

        return $this->getUserService()->findUsers(
            $condition, $this->getOffset(), 100, new OrderBy([
                new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME)),
                new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME)),
            ])
        );
    }

    public function set_user_count(int $userCount): void
    {
        $this->userCount = $userCount;
    }
}
