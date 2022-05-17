<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Document\Component;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Document\Manager;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButtonDivider;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButtonHeader;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\EndsWithCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class BrowserComponent extends Manager
{
    const ACTION_DOWNLOAD_SELECTED_PUBLICATIONS = 'download_selected_publications';

    const FILTER_THIS_MONTH = 'Month';

    const FILTER_THIS_WEEK = 'Week';

    const FILTER_TODAY = 'Today';

    const PARAM_FILTER = 'filter';

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    }

    protected function getFilter()
    {
        return $this->getRequest()->query->get(self::PARAM_FILTER);
    }

    public function getFilterActions()
    {
        $showActions = [];
        $filter = $this->getFilter();

        $showActions[] = new SubButtonHeader(Translation::get('ViewPeriodHeader'));

        $showActions[] = new SubButton(
            Translation::get('PeriodAll', null, Utilities::COMMON_LIBRARIES), null,
            $this->get_url(array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => null)),
            Button::DISPLAY_LABEL, false, [], null, $filter == ''
        );

        $showActions[] = new SubButton(
            Translation::get('PeriodToday', null, Utilities::COMMON_LIBRARIES), null, $this->get_url(
            array(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => null,
                self::PARAM_FILTER => self::FILTER_TODAY
            )
        ), Button::DISPLAY_LABEL, false, [], null, $filter == self::FILTER_TODAY
        );

        $showActions[] = new SubButton(
            Translation::get('PeriodWeek', null, Utilities::COMMON_LIBRARIES), null, $this->get_url(
            array(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => null,
                self::PARAM_FILTER => self::FILTER_THIS_WEEK
            )
        ), Button::DISPLAY_LABEL, false, [], null, $filter == self::FILTER_THIS_WEEK
        );

        $showActions[] = new SubButton(
            Translation::get('PeriodMonth', null, Utilities::COMMON_LIBRARIES), null, $this->get_url(
            array(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => null,
                self::PARAM_FILTER => self::FILTER_THIS_MONTH
            )
        ), Button::DISPLAY_LABEL, false, [], null, $filter == self::FILTER_THIS_MONTH
        );

        $showActions[] = new SubButtonDivider();

        return $showActions;
    }

    public function get_additional_form_actions()
    {
        return array(
            new TableFormAction(
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_DOWNLOAD_SELECTED_PUBLICATIONS
                    )
                ), Translation::get('DownloadSelected'), false
            )
        );
    }

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PARAM_BROWSE_PUBLICATION_TYPE;

        return parent::getAdditionalParameters($additionalParameters);
    }

    public function get_tool_actions()
    {
        $toolActions = [];

        $toolActions[] = new Button(
            Translation::get('Download'), new FontAwesomeGlyph('download'),
            $this->get_url(array(self::PARAM_ACTION => self::ACTION_ZIP_AND_DOWNLOAD)), Button::DISPLAY_ICON_AND_LABEL
        );

        return $toolActions;
    }

    public function get_tool_conditions()
    {
        $conditions = [];
        $filter = $this->getFilter();

        switch ($filter)
        {
            case self::FILTER_TODAY :
                $time = mktime(0, 0, 0, date('m', time()), date('d', time()), date('Y', time()));
                break;
            case self::FILTER_THIS_WEEK :
                $time = strtotime('Next Monday', strtotime('-1 Week', time()));
                break;
            case self::FILTER_THIS_MONTH :
                $time = mktime(0, 0, 0, date('m', time()), 1, date('Y', time()));
                break;
        }

        if ($filter)
        {
            $conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(
                    ContentObjectPublication::class, ContentObjectPublication::PROPERTY_MODIFIED_DATE
                ), ComparisonCondition::GREATER_THAN_OR_EQUAL, new StaticConditionVariable($time)
            );
        }

        $browser_type = $this->get_browser_type();
        if ($browser_type == ContentObjectPublicationListRenderer::TYPE_GALLERY ||
            $browser_type == ContentObjectPublicationListRenderer::TYPE_SLIDESHOW)
        {
            $classes = [];

            if (ContentObject::is_available('Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File'))
            {
                $classes[] = File::class;
            }

            $image_subselect_conditions = [];

            foreach ($classes as $class)
            {
                $image_types = $class::get_image_types();
                $image_conditions = [];
                foreach ($image_types as $image_type)
                {
                    $image_conditions[] = new EndsWithCondition(
                        new PropertyConditionVariable($class, $class::PROPERTY_FILENAME), $image_type
                    );
                }

                $image_condition = new OrCondition($image_conditions);

                $image_subselect_conditions[] = new SubselectCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication::class, ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID
                    ), new PropertyConditionVariable($class, $class::PROPERTY_ID), $class::get_table_name(),
                    $image_condition
                );
            }

            $conditions[] = new OrCondition($image_subselect_conditions);
        }

        return $conditions;
    }
}
