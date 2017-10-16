<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Preview\Component;

use Chamilo\Core\Repository\ContentObject\Wiki\Display\WikiDisplaySupport;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Platform\Translation;

class ViewerComponent extends \Chamilo\Core\Repository\Display\Preview implements WikiDisplaySupport
{

    public function run()
    {
        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Repository\ContentObject\Wiki\Display\Manager::context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this))->run();
    }

    /**
     * Functionality is publication dependent, so not available in preview mode.
     */
    public function get_wiki_page_statistics_reporting_template_name()
    {
        throw new UserException(Translation::get('ImpossibleInPreviewMode'));
    }

    /**
     * Functionality is publication dependent, so not available in preview mode.
     */
    public function get_wiki_statistics_reporting_template_name()
    {
        throw new UserException(Translation::get('ImpossibleInPreviewMode'));
    }
}
