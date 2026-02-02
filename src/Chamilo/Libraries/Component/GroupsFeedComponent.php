<?php
namespace Chamilo\Libraries\Component;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Manager;
use Chamilo\Libraries\Protocol\Ajax\Architecture\Domain\JsonAjaxResult;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderProperty;
use Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Response;

/**
 * Feed to return the course groups of this course
 *
 * @author  Sven Vanpoucke
 * @package Chamilo\Libraries\Ajax\Component
 */
abstract class GroupsFeedComponent extends Manager
{
    public const PARAM_FILTER = 'filter';
    public const PARAM_OFFSET = 'offset';
    public const PARAM_SEARCH_QUERY = 'query';
    public const PROPERTY_ELEMENTS = 'elements';
    public const PROPERTY_TOTAL_ELEMENTS = 'total_elements';

    /**
     * @var int
     */
    protected $userCount = 0;

    public function run(): Response
    {
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
     * Returns all the elements for this feed
     *
     * @return \Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\AdvancedElementFinder\AdvancedElementFinderElements
     */
    private function getElements()
    {
        $elements = new AdvancedElementFinderElements();
        $glyph = new FontAwesomeGlyph('folder', [], null, 'fas');

        // Add groups
        $groups = $this->retrieveGroups();
        if ($groups && $groups->count() > 0) {
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
        $users = $this->retrieve_users();
        if ($users && $users->count() > 0) {
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
     * @return \Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\AdvancedElementFinder\AdvancedElementFinderElement
     */
    abstract public function getGroupElement(Group $group): AdvancedElementFinderElement;

    /**
     * @return int
     */
    protected function getOffset()
    {
        $offset = $this->getRequest()->request->get(self::PARAM_OFFSET);
        if (!isset($offset) || is_null($offset)) {
            $offset = 0;
        }

        return $offset;
    }

    /**
     * @return \Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator
     */
    protected function getSearchQueryConditionGenerator()
    {
        return $this->getService(SearchQueryConditionGenerator::class);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\AdvancedElementFinder\AdvancedElementFinderElement
     */
    abstract public function getUserElement(User $user): AdvancedElementFinderElement;

    /**
     * @return int[]
     */
    abstract public function getUserIdentifiers();

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     */
    abstract public function retrieveGroups();

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     */
    private function retrieve_users()
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
            $conditions[] = $this->getSearchQueryConditionGenerator()->getSearchConditions(
                $searchQuery, [
                    new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME),
                    new PropertyConditionVariable(User::class, User::PROPERTY_GIVEN_NAME),
                    new PropertyConditionVariable(User::class, User::PROPERTY_SURNAME)
                ]
            );
        }

        $condition = new AndCondition($conditions);

        $this->userCount = $this->getUserService()->countUsers($condition);

        return $this->getUserService()->findUsers(
            $condition, $this->getOffset(), 100, new OrderBy([
                new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_SURNAME)),
                new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_GIVEN_NAME))
            ])
        );
    }
}
