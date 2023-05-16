<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Table;

use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\User\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SubSubscribedPlatformGroupTableRenderer extends DataClassListTableRenderer
{
    public const PROPERTY_SUBGROUPS = 'Subgroups';
    public const PROPERTY_USERS = 'Users';

    protected GroupsTreeTraverser $groupsTreeTraverser;

    public function __construct(
        GroupsTreeTraverser $groupsTreeTraverser, Translator $translator, UrlGenerator $urlGenerator,
        ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        $this->groupsTreeTraverser = $groupsTreeTraverser;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );
    }

    public function getGroupsTreeTraverser(): GroupsTreeTraverser
    {
        return $this->groupsTreeTraverser;
    }

    protected function initializeColumns()
    {
        $translator = $this->getTranslator();

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(Group::class, Group::PROPERTY_NAME)
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(Group::class, Group::PROPERTY_CODE)
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(Group::class, Group::PROPERTY_DESCRIPTION)
        );
        $this->addColumn(
            new StaticTableColumn(
                self::PROPERTY_USERS, $translator->trans(self::PROPERTY_USERS, [], \Chamilo\Core\User\Manager::CONTEXT)
            )
        );
        $this->addColumn(
            new StaticTableColumn(
                self::PROPERTY_SUBGROUPS, $translator->trans(self::PROPERTY_SUBGROUPS, [], Manager::CONTEXT)
            )
        );
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @throws \Exception
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $group): string
    {
        $urlGenerator = $this->getUrlGenerator();

        switch ($column->get_name())
        {
            case Group::PROPERTY_NAME :
                $title = parent::renderCell($column, $resultPosition, $group);
                $title_short = $title;
                if (strlen($title_short) > 53)
                {
                    $title_short = mb_substr($title_short, 0, 50) . '&hellip;';
                }

                return $title_short;
            case Group::PROPERTY_DESCRIPTION :
                return $this->getGroupsTreeTraverser()->getFullyQualifiedNameForGroup($group);
            case self::PROPERTY_USERS:
                return (string) $this->getGroupsTreeTraverser()->countUsersForGroup($group);
            case self::PROPERTY_SUBGROUPS:
                return (string) $this->getGroupsTreeTraverser()->countSubGroupsForGroup($group, true);
        }

        return parent::renderCell($column, $resultPosition, $group);
    }
}
