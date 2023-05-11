<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table;

use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\AssignmentDataProvider;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataClass\Publication;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Publication as PublicationAlias;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ObjectPublicationTableRenderer extends \Chamilo\Application\Weblcms\Table\ObjectPublicationTableRenderer
{
    private function generate_title_link($publication): string
    {
        $url = $this->getUrlGenerator()->fromRequest(
            [
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[DataClass::PROPERTY_ID],
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_DISPLAY
            ]
        );

        return '<a href="' . $url . '">' .
            StringUtilities::getInstance()->truncate($publication[ContentObject::PROPERTY_TITLE], 50) . '</a>';
    }

    protected function getAssignmentDataProvider(ContentObjectPublication $contentObjectPublication
    ): AssignmentDataProvider
    {
        /** @var \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\BrowserComponent $component */
        $component = $this->contentObjectPublicationListRenderer->get_tool_browser()->get_parent();

        $dataProvider = $component->getAssignmentDataProvider();
        $dataProvider->setContentObjectPublication($contentObjectPublication);

        return $dataProvider;
    }

    protected function getAssignmentPublication(ContentObjectPublication $contentObjectPublication)
    {
        /** @var \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\BrowserComponent $component */
        $component = $this->contentObjectPublicationListRenderer->get_tool_browser()->get_parent();

        return $component->getAssignmentPublication($contentObjectPublication);
    }

    protected function getAssignmentService(): AssignmentService
    {
        /** @var \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\BrowserComponent $component */
        $component = $this->contentObjectPublicationListRenderer->get_tool_browser()->get_parent();

        return $component->getAssignmentService();
    }

    public function getTableActions(): TableActions
    {
        $tableActions = parent::getTableActions();
        $tableActions->setNamespace(__NAMESPACE__);

        return $tableActions;
    }

    protected function initializeColumns(): void
    {
        parent::initializeColumns();

        $this->addColumn(
            new DataClassPropertyTableColumn(Assignment::class, Assignment::PROPERTY_END_TIME, null, false)
        );

        $this->addColumn(
            new StaticTableColumn(
                Manager::PROPERTY_NUMBER_OF_SUBMISSIONS,
                $this->getTranslator()->trans('NumberOfSubmissions', [], Manager::CONTEXT)
            )
        );

        $this->addColumn(new StaticTableColumn(Publication::PROPERTY_ENTITY_TYPE, ''), 1);
    }

    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $publication): string
    {
        $content_object =
            $this->contentObjectPublicationListRenderer->get_content_object_from_publication($publication);

        switch ($column->get_name())
        {
            case ContentObject::PROPERTY_TITLE :
                return $this->generate_title_link($publication);
            case Assignment::PROPERTY_END_TIME :
                $time = $content_object->get_end_time();
                $date_format = $this->getTranslator()->trans('DateTimeFormatLong', [], StringUtilities::LIBRARIES);
                $time = $this->getDatetimeUtilities()->formatLocaleDate($date_format, $time);

                if ($publication[ContentObjectPublication::PROPERTY_HIDDEN])
                {
                    return '<span style="color: gray">' . $time . '</span>';
                }

                return $time;
            case Manager::PROPERTY_NUMBER_OF_SUBMISSIONS :
                $contentObjectPublication = new ContentObjectPublication();
                $contentObjectPublication->setId($publication[DataClass::PROPERTY_ID]);

                $contentObjectPublication->set_content_object_id(
                    (int) $publication[PublicationAlias::PROPERTY_CONTENT_OBJECT_ID]
                );

                $entityType = $this->getAssignmentPublication($contentObjectPublication)->getEntityType();

                $entitiesCount =
                    $this->getAssignmentDataProvider($contentObjectPublication)->countEntitiesByEntityType($entityType);

                $entitiesWithEntriesCount =
                    $this->getAssignmentService()->countDistinctEntriesByContentObjectPublicationAndEntityType(
                        $contentObjectPublication, $entityType
                    );

                return $entitiesWithEntriesCount . ' / ' . $entitiesCount;
            case Publication::PROPERTY_ENTITY_TYPE:

                $contentObjectPublication = new ContentObjectPublication();
                $contentObjectPublication->setId($publication[DataClass::PROPERTY_ID]);

                $contentObjectPublication->set_content_object_id(
                    (int) $publication[PublicationAlias::PROPERTY_CONTENT_OBJECT_ID]
                );

                $entityType = $this->getAssignmentPublication($contentObjectPublication)->getEntityType();

                $entityTypeName =
                    $this->getAssignmentDataProvider($contentObjectPublication)->getPluralEntityNameByType($entityType);

                $iconName = ($entityType == Entry::ENTITY_TYPE_USER) ? 'user' : 'users';

                $glyph = new FontAwesomeGlyph($iconName, [], $entityTypeName);

                return $glyph->render();
        }

        return parent::renderCell($column, $resultPosition, $publication); // TODO: Change the autogenerated stub
    }

}