<?php
namespace Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Component;

use Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Manager;
use Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator;

/**
 * Feed to return users from the user entity
 *
 * @package Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserEntityFeedComponent extends Manager
{

    const PARAM_OFFSET = 'offset';

    const PARAM_SEARCH_QUERY = 'query';

    const PROPERTY_ELEMENTS = 'elements';

    const PROPERTY_TOTAL_ELEMENTS = 'total_elements';

    /**
     * Runs this ajax component
     */
    public function run()
    {
        $result = new JsonAjaxResult();

        $result->set_property(self::PROPERTY_ELEMENTS, $this->getElements()->as_array());
        $result->set_property(self::PROPERTY_TOTAL_ELEMENTS, $this->countUsers());

        $result->display();
    }

    /**
     * @return integer
     */
    protected function countUsers()
    {
        $searchQuery = $this->getRequest()->request->get(self::PARAM_SEARCH_QUERY);

        return $this->getUserService()->countUsersForSearchQuery($searchQuery);
    }

    /**
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function findUsers()
    {
        $searchQuery = $this->getRequest()->request->get(self::PARAM_SEARCH_QUERY);

        return $this->getUserService()->findUsersForSearchQuery($searchQuery, $this->getOffset(), 100);
    }

    /**
     * Returns the advanced element finder element for the given user
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement
     */
    protected function getElementForUser($user)
    {
        return new AdvancedElementFinderElement(
            UserEntityProvider::ENTITY_TYPE . '_' . $user->getId(), 'type type_user', $user->get_fullname(),
            $user->get_official_code()
        );
    }

    /**
     * Returns all the elements for this feed
     *
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements
     */
    private function getElements()
    {
        $elements = new AdvancedElementFinderElements();
        $label = $this->getTranslator()->trans('Users', [], 'Chamilo\Core\User');

        // Add user category
        $userCategory = new AdvancedElementFinderElement('users', 'category', $label, $label);
        $elements->add_element($userCategory);

        foreach ($this->findUsers() as $user)
        {
            $userCategory->add_child($this->getElementForUser($user));
        }

        return $elements;
    }

    /**
     * Returns the selected offset
     *
     * @return integer
     */
    protected function getOffset()
    {
        return $this->getRequest()->request->get(self::PARAM_OFFSET, 0);
    }

    /**
     * @return \Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator
     */
    protected function getSearchQueryConditionGenerator()
    {
        return $this->getService(SearchQueryConditionGenerator::class);
    }
}
