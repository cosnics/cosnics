<?php
namespace Chamilo\Core\Repository\Preview\Component;

use Chamilo\Core\Repository\Preview\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Platform\Translation;

class DisplayComponent extends Manager
{

    /**
     * Executes this controller
     */
    public function run()
    {
        if (! \Chamilo\Core\Repository\Display\Manager::exists(
            $this->get_content_object()->package() . '\Display\Preview'))
        {
            throw new NoObjectSelectedException(Translation::get('ContentObject'));
        }

        $context = $this->get_content_object()->package() . '\Display\Preview';

        return $this->getApplicationFactory()->getApplication(
            $context,
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this))->run();
    }

    /**
     *
     * @return \core\repository\ContentObject
     */
    public function get_root_content_object()
    {
        return $this->get_content_object();
    }
}
