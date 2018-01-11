<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Publication;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\Assignment\Entry;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Table\Publication\Table\ObjectPublicationTableCellRenderer;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataClass\Publication;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Extension on the content object publication table cell renderer for this tool
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PublicationTableCellRenderer extends ObjectPublicationTableCellRenderer
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Renders a cell for a given object
     *
     * @param $column \libraries\ObjectTableColumn
     *
     * @param mixed $publication
     *
     * @return String
     */
    public function render_cell($column, $publication)
    {
        $content_object = $this->get_component()->get_content_object_from_publication($publication);

        switch ($column->get_name())
        {
            case ContentObject::PROPERTY_TITLE :
                return $this->generate_title_link($publication);
            case Assignment::PROPERTY_END_TIME :
                $time = $content_object->get_end_time();
                $date_format = Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES);
                $time = DatetimeUtilities::format_locale_date($date_format, $time);
                if ($publication[ContentObjectPublication::PROPERTY_HIDDEN])
                {
                    return '<span style="color: gray">' . $time . '</span>';
                }

                return $time;
            case Manager::PROPERTY_NUMBER_OF_SUBMISSIONS :
                $contentObjectPublication = new ContentObjectPublication();
                $contentObjectPublication->setId($publication[DataClass::PROPERTY_ID]);

                $contentObjectPublication->set_content_object_id(
                    $publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]
                );

                $entityType = $this->getAssignmentPublication($contentObjectPublication)->getEntityType();

                $entitiesCount =
                    $this->getAssignmentDataProvider($contentObjectPublication)->countEntitiesByEntityType($entityType);

                $entitiesWithEntriesCount =
                    $this->getAssignmentService()->countDistinctEntriesByContentObjectPublicationAndEntityType(
                        $contentObjectPublication,
                        $entityType
                    );

                return $entitiesWithEntriesCount . ' / ' . $entitiesCount;
            case Publication::PROPERTY_ENTITY_TYPE:

                $contentObjectPublication = new ContentObjectPublication();
                $contentObjectPublication->setId($publication[DataClass::PROPERTY_ID]);

                $contentObjectPublication->set_content_object_id(
                    $publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]
                );

                $entityType = $this->getAssignmentPublication($contentObjectPublication)->getEntityType();

                $entityTypeName =
                    $this->getAssignmentDataProvider($contentObjectPublication)->getPluralEntityNameByType($entityType);

                $iconName = ($entityType == Entry::ENTITY_TYPE_USER) ? 'user' : 'users';

                $glyph = new FontAwesomeGlyph($iconName, [], $entityTypeName);

                return $glyph->render();
        }

        return parent::render_cell($column, $publication);
    }

    /**
     * Generated the HTML for the title column, including link, depending on the status of the current browsing user.
     *
     * @param $publication type The publication for which the title link is to be generated.
     *
     * @return string The HTML for the link in the title column.
     */
    private function generate_title_link($publication)
    {
        $url = $this->get_component()->get_url(
            array(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID],
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_DISPLAY
            )
        );

        return '<a href="' . $url . '">' .
            StringUtilities::getInstance()->truncate($publication[ContentObject::PROPERTY_TITLE], 50) . '</a>';
    }

    /**
     * @return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService
     */
    protected function getAssignmentService()
    {
        /** @var \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\BrowserComponent $component */
        $component = $this->get_component()->get_tool_browser()->get_parent();

        return $component->getAssignmentService();
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\AssignmentDataProvider
     */
    protected function getAssignmentDataProvider(ContentObjectPublication $contentObjectPublication)
    {
        /** @var \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\BrowserComponent $component */
        $component = $this->get_component()->get_tool_browser()->get_parent();

        $dataProvider = $component->getAssignmentDataProvider();
        $dataProvider->setContentObjectPublication($contentObjectPublication);

        return $dataProvider;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataClass\Publication|\Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    protected function getAssignmentPublication(ContentObjectPublication $contentObjectPublication)
    {
        /** @var \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\BrowserComponent $component */
        $component = $this->get_component()->get_tool_browser()->get_parent();

        return $component->getAssignmentPublication($contentObjectPublication);
    }

}