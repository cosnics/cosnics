<?php
namespace Chamilo\Application\Weblcms\Request\Rights\Table;

use Chamilo\Application\Weblcms\Request\Rights\Manager;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Request\Rights\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EntityTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const PROPERTY_ENTITY = 'entity';
    public const PROPERTY_GROUP = 'group';
    public const PROPERTY_PATH = 'path';
    public const PROPERTY_TYPE = 'type';

    public const TABLE_IDENTIFIER = Manager::PARAM_LOCATION_ENTITY_RIGHT_GROUP_ID;

    protected GroupService $groupService;

    protected User $user;

    protected UserService $userService;

    public function __construct(
        GroupService $groupService, UserService $userService, User $user, Translator $translator,
        UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager
    )
    {
        $this->groupService = $groupService;
        $this->userService = $userService;
        $this->user = $user;

        parent::__construct($translator, $urlGenerator, $htmlTableRenderer, $pager);
    }

    public function getGroupService(): GroupService
    {
        return $this->groupService;
    }

    public function getTableActions(): TableActions
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $actions->addAction(
            new TableAction(
                $urlGenerator->fromRequest([Manager::PARAM_ACTION => Manager::ACTION_DELETE]),
                $translator->trans('RemoveSelected', [], StringUtilities::LIBRARIES)
            )
        );

        return $actions;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }

    protected function initializeColumns()
    {
        $translator = $this->getTranslator();

        $this->addColumn(new StaticTableColumn(self::PROPERTY_TYPE, $translator->trans('Type', [], Manager::CONTEXT)));
        $this->addColumn(
            new StaticTableColumn(self::PROPERTY_ENTITY, $translator->trans('Entity', [], Manager::CONTEXT))
        );
        $this->addColumn(
            new StaticTableColumn(self::PROPERTY_GROUP, $translator->trans('Group', [], Manager::CONTEXT))
        );
        $this->addColumn(new StaticTableColumn(self::PROPERTY_PATH, $translator->trans('Path', [], Manager::CONTEXT)));
    }

    /**
     * @throws \ReflectionException
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $object): string
    {
        switch ($column->get_name())
        {
            case self::PROPERTY_TYPE:
                $location_entity_right = $object->get_location_entity_right();

                switch ($location_entity_right->get_entity_type())
                {
                    case UserEntity::ENTITY_TYPE :
                        $context = 'Chamilo\Core\User';
                        break;
                    case PlatformGroupEntity::ENTITY_TYPE :
                        $context = 'Chamilo\Core\Group';
                        break;
                    default:
                        return '';
                }

                $glyph = new NamespaceIdentGlyph(
                    $context, true, false, false, IdentGlyph::SIZE_MINI
                );

                return $glyph->render();
            case self::PROPERTY_ENTITY:
                $location_entity_right = $object->get_location_entity_right();
                switch ($location_entity_right->get_entity_type())
                {
                    case UserEntity::ENTITY_TYPE :
                        return $this->getUserService()->findUserByIdentifier($location_entity_right->get_entity_id())
                            ->get_fullname();
                    case PlatformGroupEntity::ENTITY_TYPE :
                        return $this->getGroupService()->findGroupByIdentifier(
                            (int) $location_entity_right->get_entity_id()
                        )->get_name();
                    default:
                        return '';
                }
            case self::PROPERTY_GROUP:
                return $object->get_group()->get_name();
            case self::PROPERTY_PATH:
                return $object->get_group()->get_fully_qualified_name();
        }

        return parent::renderCell($column, $resultPosition, $object);
    }

    public function renderTableRowActions(TableResultPosition $resultPosition, $object): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        if ($this->getUser()->is_platform_admin())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                    $urlGenerator->fromRequest(
                        [
                            Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                            Manager::PARAM_LOCATION_ENTITY_RIGHT_GROUP_ID => $object->get_id()
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->render();
    }
}
