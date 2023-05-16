<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Table;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupUsersTableRenderer extends DataClassListTableRenderer
{
    protected ConfigurationConsulter $configurationConsulter;

    public function __construct(
        ConfigurationConsulter $configurationConsulter, Translator $translator, UrlGenerator $urlGenerator,
        ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        $this->configurationConsulter = $configurationConsulter;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    protected function initializeColumns()
    {
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_LASTNAME)
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_FIRSTNAME)
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_USERNAME)
        );

        $showEmail = $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\User', 'show_email_addresses']);

        if ($showEmail)
        {
            $this->addColumn(
                $this->getDataClassPropertyTableColumnFactory()->getColumn(User::class, User::PROPERTY_EMAIL)
            );
        }
    }
}
