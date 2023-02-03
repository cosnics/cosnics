<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Table;

use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActivityTableRenderer extends DataClassListTableRenderer
{
    public const DEFAULT_ORDER_COLUMN_DIRECTION = SORT_DESC;
    public const DEFAULT_ORDER_COLUMN_INDEX = 4;

    public const PROPERTY_TYPE_ICON = 'type_icon';
    public const PROPERTY_USER = 'user';

    protected DatetimeUtilities $datetimeUtilities;

    public function __construct(
        DatetimeUtilities $datetimeUtilities, Translator $translator, UrlGenerator $urlGenerator,
        ListHtmlTableRenderer $htmlTableRenderer, Pager $pager
    )
    {
        $this->datetimeUtilities = $datetimeUtilities;

        parent::__construct($translator, $urlGenerator, $htmlTableRenderer, $pager);
    }

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    protected function initializeColumns()
    {
        $translator = $this->getTranslator();
        $glyph = new FontAwesomeGlyph('mouse', [], $translator->trans('ActivityType', [], Manager::CONTEXT), 'fas');

        $this->addColumn(new StaticTableColumn(self::PROPERTY_TYPE_ICON, $glyph->render()));
        $this->addColumn(new DataClassPropertyTableColumn(Activity::class, Activity::PROPERTY_TYPE));
        $this->addColumn(new DataClassPropertyTableColumn(Activity::class, Activity::PROPERTY_CONTENT));
        $this->addColumn(new StaticTableColumn(self::PROPERTY_USER, $translator->trans('User')));
        $this->addColumn(new DataClassPropertyTableColumn(Activity::class, Activity::PROPERTY_DATE));
    }

    /**
     * @param \Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity $activity
     *
     * @throws \Exception
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $activity): string
    {
        $translator = $this->getTranslator();

        switch ($column->get_name())
        {
            case self::PROPERTY_TYPE_ICON :
                return Activity::type_image($activity->getType());
            case Activity::PROPERTY_TYPE :
                return $activity->get_type_string();
            case Activity::PROPERTY_DATE :
                $date_format = $translator->trans('DateTimeFormatLong', [], StringUtilities::LIBRARIES);

                return $this->getDatetimeUtilities()->formatLocaleDate($date_format, $activity->get_date());
            case self::PROPERTY_USER :
                return $activity->get_user()->get_fullname();
        }

        return parent::renderCell($column, $resultPosition, $activity);
    }
}
