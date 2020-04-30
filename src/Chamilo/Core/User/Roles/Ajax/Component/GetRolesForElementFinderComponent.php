<?php
namespace Chamilo\Core\User\Roles\Ajax\Component;

use Chamilo\Core\User\Roles\Ajax\Manager;
use Chamilo\Core\User\Roles\Service\Interfaces\RoleServiceInterface;
use Chamilo\Core\User\Roles\Service\RoleService;
use Chamilo\Core\User\Roles\Storage\DataClass\Role;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\Ajax\AjaxResultDataProviderInterface;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\Ajax\AjaxResultGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * Returns the courses formatted for the element finder
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GetRolesForElementFinderComponent extends Manager implements AjaxResultDataProviderInterface
{
    const PARAM_OFFSET = 'offset';

    const PARAM_SEARCH_QUERY = 'query';

    /**
     *
     * @var AjaxResultGenerator
     */
    protected $ajaxResultGenerator;

    /**
     * Runs this component and returns it's response
     */
    public function run()
    {
        $this->ajaxResultGenerator = new AjaxResultGenerator(
            $this, $this->getRequest()->get(self::PARAM_SEARCH_QUERY), $this->getRequest()->get(self::PARAM_OFFSET)
        );

        $this->ajaxResultGenerator->generateAjaxResult()->display();
    }

    /**
     * Generates the elements for the advanced element finder
     *
     * @param AdvancedElementFinderElements $advancedElementFinderElements
     */
    public function generateElements(AdvancedElementFinderElements $advancedElementFinderElements)
    {
        $roles = $this->getRoles();
        $glyph = new FontAwesomeGlyph('mask', array(), null, 'fas');

        foreach ($roles as $role)
        {
            $advancedElementFinderElements->add_element(
                new AdvancedElementFinderElement(
                    'role_' . $role->getId(), $glyph->getClassNamesString(), $role->getRole(), $role->getRole()
                )
            );
        }
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    protected function getCondition()
    {
        return $this->ajaxResultGenerator->getSearchCondition(
            array(new PropertyConditionVariable(Role::class, Role::PROPERTY_ROLE))
        );
    }

    /**
     *
     * @return RoleServiceInterface
     */
    protected function getRoleService()
    {
        return $this->getService(RoleService::class);
    }

    /**
     * Retrieves the courses for the current request
     *
     * @return Role[]
     */
    protected function getRoles()
    {
        return $this->getRoleService()->getRoles(
            $this->getCondition(), 100, $this->ajaxResultGenerator->getOffset(),
            array(new OrderBy(new PropertyConditionVariable(Role::class, Role::PROPERTY_ROLE)))
        );
    }

    /**
     * Returns the number of total elements (without the offset)
     *
     * @return int
     */
    public function getTotalNumberOfElements()
    {
        return $this->getRoleService()->countRoles($this->getCondition());
    }
}