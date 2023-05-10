<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroupUserRelation;
use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordListTableRenderer;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CourseGroupUserTableRenderer extends RecordListTableRenderer
{
    protected ConfigurationConsulter $configurationConsulter;

    protected DatetimeUtilities $datetimeUtilities;

    public function __construct(
        ConfigurationConsulter $configurationConsulter, DatetimeUtilities $datetimeUtilities, Translator $translator,
        UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager
    )
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->datetimeUtilities = $datetimeUtilities;

        parent::__construct($translator, $urlGenerator, $htmlTableRenderer, $pager);
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    protected function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_OFFICIAL_CODE));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_USERNAME));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_LASTNAME));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_FIRSTNAME));

        $showEmail = $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\User', 'show_email_addresses']);

        if ($showEmail)
        {
            $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_EMAIL));
        }

        $this->addColumn(
            new DataClassPropertyTableColumn(
                CourseGroupUserRelation::class, CourseGroupUserRelation::PROPERTY_SUBSCRIPTION_TIME
            )
        );
    }

    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $record): string
    {
        if ($column->get_name() == CourseGroupUserRelation::PROPERTY_SUBSCRIPTION_TIME)
        {
            $subscriptionTime = $record[CourseGroupUserRelation::PROPERTY_SUBSCRIPTION_TIME];

            if ($subscriptionTime)
            {
                return $this->getDatetimeUtilities()->formatLocaleDate(
                    $this->getTranslator()->trans('SubscriptionTimeFormat', [], Manager::CONTEXT),
                    (int) $subscriptionTime
                );
            }

            return '';
        }

        return parent::renderCell($column, $resultPosition, $record);
    }
}
