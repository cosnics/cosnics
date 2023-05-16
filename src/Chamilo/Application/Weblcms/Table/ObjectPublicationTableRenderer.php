<?php
namespace Chamilo\Application\Weblcms\Table;

use Chamilo\Application\Weblcms\Manager as WeblcmsManager;
use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Rights\Entities\CourseGroupEntity;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Table\Publication\Table\ObjectPublicationTableColumnModel;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager as CourseGroupDataManager;
use Chamilo\Application\Weblcms\Tool\Manager;
use Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\TableParameterValues;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

/**
 * @package Chamilo\Application\Weblcms\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ObjectPublicationTableRenderer extends RecordListTableRenderer
    implements TableActionsSupport, TableRowActionsSupport
{
    public const DEFAULT_ORDER_COLUMN_INDEX = 7;

    public const PROPERTY_PUBLISHED_FOR = 'published_for';
    public const PROPERTY_STATUS = 'status';

    public const TABLE_IDENTIFIER = Manager::PARAM_PUBLICATION_ID;

    /**
     * @deprecated Temporary solution to allow rendering of DI-based tables in a non-DI context
     */
    protected ContentObjectPublicationListRenderer $contentObjectPublicationListRenderer;

    protected DatetimeUtilities $datetimeUtilities;

    protected GroupService $groupService;

    protected UserService $userService;

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    public function getDisplayOrderColumnProperty(): string
    {
        return ContentObjectPublication::PROPERTY_DISPLAY_ORDER_INDEX;
    }

    public function getGroupService(): GroupService
    {
        return $this->groupService;
    }

    public function getTableActions(): TableActions
    {
        return $this->contentObjectPublicationListRenderer->get_actions();
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }

    /**
     * @return void
     */
    protected function initializeColumns(): void
    {
        $translator = $this->getTranslator();

        $this->addColumn(new StaticTableColumn(self::PROPERTY_STATUS, '', null, ['publication_table_status_column']));

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                ContentObject::class, ContentObject::PROPERTY_TITLE
            )
        );

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                ContentObject::class, ContentObject::PROPERTY_DESCRIPTION
            )
        );

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_PUBLICATION_DATE
            )
        );

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                ContentObject::class, ContentObject::PROPERTY_MODIFICATION_DATE
            )
        );

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_PUBLISHER_ID
            )
        );

        $this->addColumn(
            new StaticTableColumn(
                self::PROPERTY_PUBLISHED_FOR, $translator->trans(
                'PublishedFor', [], WeblcmsManager::CONTEXT
            )
            )
        );

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_DISPLAY_ORDER_INDEX, null, true,
                null, ['publication_table_order_column']
            )
        );
    }

    public function isDisplayOrderColumn(TableResultPosition $resultPosition): bool
    {
        $orderedColumn = $this->getColumn($resultPosition->getOrderColumnIndex());

        if ($orderedColumn instanceof DataClassPropertyTableColumn &&
            $orderedColumn->get_name() == $this->getDisplayOrderColumnProperty() &&
            $resultPosition->getOrderColumnDirection() == SORT_ASC)
        {
            return true;
        }

        return false;
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     * @deprecated Temporary solution to allow rendering of DI-based tables in a non-DI context
     */
    public function legacyRender(
        ContentObjectPublicationListRenderer $contentObjectPublicationListRenderer,
        TableParameterValues $parameterValues, ArrayCollection $tableData, ?string $tableName = null
    ): string
    {
        $this->contentObjectPublicationListRenderer = $contentObjectPublicationListRenderer;

        return parent::render($parameterValues, $tableData, $tableName); // TODO: Change the autogenerated stub
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \ReflectionException
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $publication): string
    {
        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();
        $datetimeUtilities = $this->getDatetimeUtilities();

        $content_object =
            $this->contentObjectPublicationListRenderer->get_content_object_from_publication($publication);

        switch ($column->get_name())
        {
            case ObjectPublicationTableColumnModel::COLUMN_STATUS :
                $extraClasses = [];
                $titleExtra = '';

                if ($publication[ContentObjectPublication::PROPERTY_HIDDEN])
                {
                    $isAvailable = false;
                    $titleExtra .= ' ' . $translator->trans('NotAvailable', [], WeblcmsManager::CONTEXT) . ')';
                }
                else
                {
                    $isAvailable = true;
                    $last_visit_date =
                        $this->contentObjectPublicationListRenderer->get_tool_browser()->get_last_visit_date();
                    if ($publication[ContentObjectPublication::PROPERTY_PUBLICATION_DATE] >= $last_visit_date)
                    {
                        $extraClasses[] = 'fas-ci-new';
                        $titleExtra .= ' (' . $translator->trans('New', [], WeblcmsManager::CONTEXT) . ')';
                    }
                }

                $glyph = $content_object->getGlyph(
                    IdentGlyph::SIZE_MINI, $isAvailable, $extraClasses
                );
                $glyph->setTitle($glyph->getTitle() . $titleExtra);

                return $glyph->render();
            case ContentObject::PROPERTY_TITLE :
                if ($content_object instanceof ComplexContentObjectSupport)
                {
                    $details_url = $urlGenerator->fromRequest(
                        [
                            Manager::PARAM_PUBLICATION_ID => $publication[DataClass::PROPERTY_ID],
                            Manager::PARAM_ACTION => Manager::ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT
                        ]
                    );

                    return '<a href="' . $details_url . '">' .
                        parent::renderCell($column, $resultPosition, $publication) . '</a>';
                }

                $details_url = $urlGenerator->fromRequest(
                    [
                        Manager::PARAM_PUBLICATION_ID => $publication[DataClass::PROPERTY_ID],
                        Manager::PARAM_ACTION => Manager::ACTION_VIEW
                    ]
                );

                return '<a href="' . $details_url . '">' . parent::renderCell($column, $resultPosition, $publication) .
                    '</a>';
            case ContentObjectPublication::PROPERTY_PUBLICATION_DATE :
                $date_format = $translator->trans('DateTimeFormatLong', [], StringUtilities::LIBRARIES);
                $data = $datetimeUtilities->formatLocaleDate(
                    $date_format, (int) $publication[ContentObjectPublication::PROPERTY_PUBLICATION_DATE]
                );
                break;
            case ContentObjectPublication::PROPERTY_MODIFIED_DATE :
                $date_format = $translator->trans('DateTimeFormatLong', [], StringUtilities::LIBRARIES);
                $data = $datetimeUtilities->formatLocaleDate(
                    $date_format, (int) $publication[ContentObjectPublication::CONTENT_OBJECT_MODIFICATION_DATE_ALIAS]
                );
                break;
            case ContentObjectPublication::PROPERTY_PUBLISHER_ID :
                $user = $this->getUserService()->findUserByIdentifier(
                    $publication[ContentObjectPublication::PROPERTY_PUBLISHER_ID]
                );
                if (!$user)
                {
                    $data = '<i>' . $translator->trans('UserUnknown', [], WeblcmsManager::CONTEXT) . '</i>';
                }
                else
                {
                    $data = $user->get_fullname();
                }
                break;
            case 'published_for' :
                $data = '<div style="float: left;">' . $this->renderPublicationTargets($publication) . '</div>';

                if ($publication[ContentObjectPublication::PROPERTY_EMAIL_SENT])
                {
                    $glyph = new FontAwesomeGlyph('envelope', [],
                        $translator->trans('SentByEmail', [], WeblcmsManager::CONTEXT));
                    $data .= ' - ' . $glyph->render();
                }
                break;
            case ContentObject::PROPERTY_DESCRIPTION :
                $data = $publication[ContentObject::PROPERTY_DESCRIPTION];
                $data = StringUtilities::getInstance()->truncate($data, 100);
                break;
            default:
                $data = null;
        }

        if ($data)
        {
            if ($publication[ContentObjectPublication::PROPERTY_HIDDEN])
            {
                return '<span style="color: gray">' . $data . '</span>';
            }
            else
            {
                return $data;
            }
        }

        return parent::renderCell($column, $resultPosition, $publication);
    }

    /**
     * @param $publication
     *
     * @return string
     * @throws \ReflectionException
     */
    public function renderPublicationTargets($publication): string
    {
        try
        {
            $target_entities = WeblcmsRights::getInstance()->get_target_entities(
                WeblcmsRights::VIEW_RIGHT, WeblcmsManager::CONTEXT, $publication[DataClass::PROPERTY_ID],
                WeblcmsRights::TYPE_PUBLICATION,
                $this->contentObjectPublicationListRenderer->get_tool_browser()->get_course_id(),
                WeblcmsRights::TREE_TYPE_COURSE
            );
        }
        catch (Exception $exception)
        {
            error_log($exception->getMessage());
            $target_entities = [];
        }

        $target_list = [];

        if (array_key_exists(0, $target_entities[0]))
        {
            $target_list[] = $this->getTranslator()->trans('Everybody', [], StringUtilities::LIBRARIES);
        }
        else
        {
            $target_list[] = '<select>';

            foreach ($target_entities as $entity_type => $entity_ids)
            {
                switch ($entity_type)
                {
                    case GroupEntityProvider::ENTITY_TYPE :
                        foreach ($entity_ids as $group_id)
                        {
                            $group = $this->getGroupService()->findGroupByIdentifier($group_id);

                            if ($group)
                            {
                                $target_list[] = '<option>' . $group->get_name() . '</option>';
                            }
                        }
                        break;
                    case UserEntityProvider::ENTITY_TYPE :
                        foreach ($entity_ids as $user_id)
                        {
                            $user = $this->getUserService()->findUserByIdentifier($user_id);

                            if ($user)
                            {
                                $target_list[] = '<option>' . $user->get_fullname() . '</option>';
                            }
                        }
                        break;
                    case CourseGroupEntity::ENTITY_TYPE :
                        foreach ($entity_ids as $course_group_id)
                        {
                            $course_group = CourseGroupDataManager::retrieve_by_id(
                                CourseGroup::class, $course_group_id
                            );

                            if ($course_group)
                            {
                                $target_list[] = '<option>' . $course_group->get_name() . '</option>';
                            }
                        }

                        break;

                    case 0 :
                        $target_list[] = '<option>Everyone</option>';
                        break;
                }
            }
            $target_list[] = '</select>';
        }

        return implode(PHP_EOL, $target_list);
    }

    public function renderTableRowActions(TableResultPosition $resultPosition, $publication): string
    {
        return $this->contentObjectPublicationListRenderer->get_publication_actions(
            $publication, $this->isDisplayOrderColumn($resultPosition),
            $resultPosition->getOrderColumnDirection() == SORT_ASC
        )->render();
    }

}