<?php
namespace Chamilo\Core\Group\Table;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Service\GroupUrlGenerator;
use Chamilo\Core\Group\Storage\DataClass\SubscribedUser;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Group\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SubscribedUserTableRenderer extends DataClassListTableRenderer
    implements TableRowActionsSupport, TableActionsSupport
{
    public const TABLE_IDENTIFIER = Manager::PARAM_GROUP_REL_USER_ID;

    protected GroupUrlGenerator $groupUrlGenerator;

    public function __construct(
        Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory, GroupUrlGenerator $groupUrlGenerator
    )
    {
        $this->groupUrlGenerator = $groupUrlGenerator;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );
    }

    public function getGroupUrlGenerator(): GroupUrlGenerator
    {
        return $this->groupUrlGenerator;
    }

    public function getTableActions(): TableActions
    {
        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $unsubscribeUrl = $urlGenerator->fromParameters([
            Application::PARAM_CONTEXT => Manager::CONTEXT,
            Application::PARAM_ACTION => Manager::ACTION_UNSUBSCRIBE_USER_FROM_GROUP
        ]);

        $actions->addAction(
            new TableAction(
                $unsubscribeUrl, $translator->trans('UnsubscribeSelected', [], Manager::CONTEXT), false
            )
        );

        return $actions;
    }

    protected function initializeColumns(): void
    {
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(SubscribedUser::class, User::PROPERTY_FIRSTNAME)
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(SubscribedUser::class, User::PROPERTY_LASTNAME)
        );
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\SubscribedUser $subscribedUser
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $subscribedUser): string
    {
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        $unsubscribeUrl = $this->getGroupUrlGenerator()->getUnsubscribeUserUrl($subscribedUser->getRelationId());

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('UnsubscribeSelected', [], Manager::CONTEXT), new FontAwesomeGlyph('times'),
                $unsubscribeUrl, ToolbarItem::DISPLAY_ICON
            )
        );

        return $toolbar->render();
    }
}
