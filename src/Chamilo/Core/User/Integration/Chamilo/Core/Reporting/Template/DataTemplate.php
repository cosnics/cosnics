<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Block\BrowserBlock;
use Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Block\CountryBlock;
use Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Block\OperatingSystemBlock;
use Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Block\ProviderBlock;
use Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Block\RefererBlock;

class DataTemplate extends ReportingTemplate
{
    const TEMPLATE_ID = '2';
    const PROPERTY_NAME = 'DataTemplateName';
    const PROPERTY_DESCRIPTION = 'DataTemplateDescription';

    public function __construct($parent)
    {
        parent :: __construct($parent);
        
        $this->add_reporting_block(new BrowserBlock($this));
        $this->add_reporting_block(new CountryBlock($this));
        $this->add_reporting_block(new OperatingSystemBlock($this));
        $this->add_reporting_block(new ProviderBlock($this));
        $this->add_reporting_block(new RefererBlock($this));
    }
}
