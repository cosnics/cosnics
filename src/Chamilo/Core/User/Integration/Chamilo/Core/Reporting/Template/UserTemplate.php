<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Block\ActiveInactiveBlock;
use Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Block\NoOfUsersBlock;
use Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Block\NoOfUsersPictureBlock;

class UserTemplate extends ReportingTemplate
{
    const TEMPLATE_ID = '3';
    const PROPERTY_NAME = 'UserTemplateName';
    const PROPERTY_DESCRIPTION = 'UserTemplateDescription';

    public function __construct($parent)
    {
        parent :: __construct($parent);
        
        $this->add_reporting_block(new ActiveInactiveBlock($this));
        $this->add_reporting_block(new NoOfUsersPictureBlock($this));
        $this->add_reporting_block(new NoOfUsersBlock($this));
    }
}
