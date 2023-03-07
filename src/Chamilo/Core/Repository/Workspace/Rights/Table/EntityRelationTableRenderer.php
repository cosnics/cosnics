<?php
namespace Chamilo\Core\Repository\Workspace\Rights\Table;

use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Repository\Workspace\Rights\Manager;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
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
 * @package Chamilo\Core\Repository\Workspace\Rights\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EntityRelationTableRenderer extends DataClassListTableRenderer
    implements TableRowActionsSupport, TableActionsSupport
{
    public const COLUMN_ENTITY = 'entity';

    public const TABLE_IDENTIFIER = Manager::PARAM_ENTITY_RELATION_ID;

    protected GroupService $groupService;

    protected RightsService $rightsService;

    protected User $user;

    protected UserService $userService;

    public function __construct(
        UserService $userService, GroupService $groupService, RightsService $rightsService, Translator $translator,
        UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager
    )
    {
        $this->userService = $userService;
        $this->groupService = $groupService;
        $this->rightsService = $rightsService;

        parent::__construct($translator, $urlGenerator, $htmlTableRenderer, $pager);
    }

    public function getGroupService(): GroupService
    {
        return $this->groupService;
    }

    private function getRightIcon($right, $translationVariable): string
    {
        switch ($right)
        {
            case RightsService::RIGHT_VIEW:
                $glyphName = 'desktop';
                break;
            case RightsService::RIGHT_ADD:
                $glyphName = 'plus';
                break;
            case RightsService::RIGHT_EDIT:
                $glyphName = 'pencil-alt';
                break;
            case RightsService::RIGHT_DELETE:
                $glyphName = 'trash-alt';
                break;
            case RightsService::RIGHT_USE:
                $glyphName = 'share-square';
                break;
            case RightsService::RIGHT_COPY:
                $glyphName = 'copy';
                break;
            case RightsService::RIGHT_MANAGE:
                $glyphName = 'cog';
                break;
            default:
                $glyphName = 'lock';
        }

        $glyph = new FontAwesomeGlyph($glyphName, [],
            $this->getTranslator()->trans($translationVariable, [], Manager::CONTEXT), 'fas');

        return $glyph->render();
    }

    private function getRightsIcon(int $right, WorkspaceEntityRelation $entityRelation): string
    {
        $state = $entityRelation->get_rights() & $right ? 'text-success' : 'text-danger';
        $glyph = new FontAwesomeGlyph('circle', [$state]);

        return $glyph->render();
    }

    public function getRightsService(): RightsService
    {
        return $this->rightsService;
    }

    public function getTableActions(): TableActions
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $deleteUrl = $urlGenerator->fromParameters(
            [Application::PARAM_CONTEXT => Manager::CONTEXT, Manager::PARAM_ACTION => Manager::ACTION_DELETE]
        );

        $actions->addAction(
            new TableAction(
                $deleteUrl, $translator->trans('DeleteSelected', [], StringUtilities::LIBRARIES), true
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
        $this->addColumn(
            new StaticTableColumn(
                self::COLUMN_ENTITY, $this->getTranslator()->trans('Entity', [], Manager::CONTEXT)
            )
        );
        $this->addColumn(
            new StaticTableColumn(
                (string) RightsService::RIGHT_VIEW, $this->getRightIcon(RightsService::RIGHT_VIEW, 'ViewRight')
            )
        );
        $this->addColumn(
            new StaticTableColumn(
                (string) RightsService::RIGHT_ADD, $this->getRightIcon(RightsService::RIGHT_ADD, 'AddRight')
            )
        );
        $this->addColumn(
            new StaticTableColumn(
                (string) RightsService::RIGHT_EDIT, $this->getRightIcon(RightsService::RIGHT_EDIT, 'EditRight')
            )
        );
        $this->addColumn(
            new StaticTableColumn(
                (string) RightsService::RIGHT_DELETE, $this->getRightIcon(RightsService::RIGHT_DELETE, 'DeleteRight')
            )
        );
        $this->addColumn(
            new StaticTableColumn(
                (string) RightsService::RIGHT_USE, $this->getRightIcon(RightsService::RIGHT_USE, 'UseRight')
            )
        );
        $this->addColumn(
            new StaticTableColumn(
                (string) RightsService::RIGHT_COPY, $this->getRightIcon(RightsService::RIGHT_COPY, 'CopyRight')
            )
        );
        $this->addColumn(
            new StaticTableColumn(
                (string) RightsService::RIGHT_MANAGE, $this->getRightIcon(RightsService::RIGHT_MANAGE, 'ManageRight')
            )
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation $entityRelation
     *
     * @throws \Exception
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $entityRelation): string
    {
        switch ($column->get_name())
        {
            case self::COLUMN_ENTITY :
                if ($entityRelation->get_entity_type() == UserEntity::ENTITY_TYPE)
                {
                    return $this->getUserService()->findUserByIdentifier((string) $entityRelation->get_entity_id())
                        ->get_fullname();
                }
                else
                {
                    return $this->getGroupService()->findGroupByIdentifier($entityRelation->get_entity_id())->get_name(
                    );
                }
            case RightsService::RIGHT_VIEW :
                return $this->getRightsIcon(RightsService::RIGHT_VIEW, $entityRelation);
            case RightsService::RIGHT_ADD :
                return $this->getRightsIcon(RightsService::RIGHT_ADD, $entityRelation);
            case RightsService::RIGHT_EDIT :
                return $this->getRightsIcon(RightsService::RIGHT_EDIT, $entityRelation);
            case RightsService::RIGHT_DELETE :
                return $this->getRightsIcon(RightsService::RIGHT_DELETE, $entityRelation);
            case RightsService::RIGHT_USE :
                return $this->getRightsIcon(RightsService::RIGHT_USE, $entityRelation);
            case RightsService::RIGHT_COPY :
                return $this->getRightsIcon(RightsService::RIGHT_COPY, $entityRelation);
            case RightsService::RIGHT_MANAGE :
                return $this->getRightsIcon(RightsService::RIGHT_MANAGE, $entityRelation);
        }

        return parent::renderCell($column, $resultPosition, $entityRelation);
    }

    /**
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation $entityRelation
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $entityRelation): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        $updateUrl = $urlGenerator->fromParameters([
            Application::PARAM_CONTEXT => Manager::CONTEXT,
            Manager::PARAM_ACTION => Manager::ACTION_UPDATE,
            Manager::PARAM_ENTITY_RELATION_ID => $entityRelation->getId()
        ]);

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                $updateUrl, ToolbarItem::DISPLAY_ICON
            )
        );

        $deleteUrl = $urlGenerator->fromParameters([
            Application::PARAM_CONTEXT => Manager::CONTEXT,
            Manager::PARAM_ACTION => Manager::ACTION_DELETE,
            Manager::PARAM_ENTITY_RELATION_ID => $entityRelation->getId()
        ]);

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'), $deleteUrl,
                ToolbarItem::DISPLAY_ICON, true
            )
        );

        return $toolbar->render();
    }
}
