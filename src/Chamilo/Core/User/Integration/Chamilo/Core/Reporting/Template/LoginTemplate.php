<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Block\LoginBlock;
use Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Block\LoginDayBlock;
use Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Block\LoginHourBlock;
use Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Block\LoginMonthBlock;
use Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Block\PlatformStatisticsBlock;

class LoginTemplate extends ReportingTemplate
{
    const TEMPLATE_ID = '1';
    const PROPERTY_NAME = 'LoginTemplateName';
    const PROPERTY_DESCRIPTION = 'LoginTemplateDescription';

    public function __construct($parent)
    {
        parent::__construct($parent);
        
        $this->add_reporting_block(new LoginDayBlock($this));
        $this->add_reporting_block(new LoginHourBlock($this));
        $this->add_reporting_block(new LoginMonthBlock($this));
        $this->add_reporting_block(new LoginBlock($this));
        $this->add_reporting_block(new PlatformStatisticsBlock($this));
    }
}
