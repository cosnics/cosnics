<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Preview\Component;

use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Repository\ContentObject\Wiki\Display\WikiDisplaySupport;

class ViewComponent extends \Chamilo\Core\Repository\Display\Preview implements WikiDisplaySupport
{

    public function run()
    {
        $factory = new ApplicationFactory(
            $this->getRequest(),
            \Chamilo\Core\Repository\ContentObject\Wiki\Display\Manager :: context(),
            $this->get_user(),
            $this);
        return $factory->run();
    }

    /**
     * Functionality is publication dependent, so not available in preview mode.
     */
    public function get_wiki_page_statistics_reporting_template_name()
    {
        $this->not_available(Translation :: get('ImpossibleInPreviewMode'));
    }

    /**
     * Functionality is publication dependent, so not available in preview mode.
     */
    public function get_wiki_statistics_reporting_template_name()
    {
        $this->not_available(Translation :: get('ImpossibleInPreviewMode'));
    }
}
