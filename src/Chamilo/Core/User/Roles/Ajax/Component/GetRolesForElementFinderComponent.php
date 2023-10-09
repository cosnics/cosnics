<?php
namespace Chamilo\Core\User\Roles\Ajax\Component;

use Chamilo\Core\User\Roles\Ajax\Manager;
use Chamilo\Core\User\Roles\Service\RoleService;
use Chamilo\Core\User\Roles\Storage\DataClass\Role;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\Ajax\AjaxResultDataProviderInterface;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\Ajax\AjaxResultGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\User\Roles\Ajax\Component
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class GetRolesForElementFinderComponent extends Manager implements AjaxResultDataProviderInterface
{
    public const PARAM_OFFSET = 'offset';
    public const PARAM_SEARCH_QUERY = 'query';

    protected AjaxResultGenerator $ajaxResultGenerator;

    public function run()
    {
        $this->getAjaxResultGenerator()->generateAjaxResult()->display();
    }

    public function generateElements(AdvancedElementFinderElements $advancedElementFinderElements): void
    {
        $roles = $this->getRoles();
        $glyph = new FontAwesomeGlyph('mask', [], null, 'fas');

        foreach ($roles as $role)
        {
            $advancedElementFinderElements->add_element(
                new AdvancedElementFinderElement(
                    'role_' . $role->getId(), $glyph->getClassNamesString(), $role->getRole(), $role->getRole()
                )
            );
        }
    }

    protected function getAjaxResultGenerator(): AjaxResultGenerator
    {
        if (!isset($this->ajaxResultGenerator))
        {
            $this->ajaxResultGenerator = new AjaxResultGenerator(
                $this, $this->getRequest()->getFromRequestOrQuery(self::PARAM_SEARCH_QUERY),
                $this->getRequest()->getFromRequestOrQuery(self::PARAM_OFFSET)
            );
        }

        return $this->ajaxResultGenerator;
    }

    protected function getCondition(): ?AndCondition
    {
        return $this->getAjaxResultGenerator()->getSearchCondition(
            [new PropertyConditionVariable(Role::class, Role::PROPERTY_ROLE)]
        );
    }

    protected function getRoleService(): RoleService
    {
        return $this->getService(RoleService::class);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Roles\Storage\DataClass\Role>
     */
    protected function getRoles(): ArrayCollection
    {
        return $this->getRoleService()->getRoles(
            $this->getCondition(), 100, $this->ajaxResultGenerator->getOffset(),
            new OrderBy([new OrderProperty(new PropertyConditionVariable(Role::class, Role::PROPERTY_ROLE))])
        );
    }

    public function getTotalNumberOfElements(): int
    {
        return $this->getRoleService()->countRoles($this->getCondition());
    }
}