<?php
namespace Chamilo\Core\Repository\Preview\Component;

use Chamilo\Core\Repository\Preview\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Translation\Translation;

class DisplayComponent extends Manager
{

    /**
     * Executes this controller
     */
    public function run()
    {
        $className = $this->get_content_object()::CONTEXT . '\Display\Preview\Manager';

        if (! class_exists($className))
        {
            throw new NoObjectSelectedException(Translation::get('ContentObject'));
        }

        $context = $this->get_content_object()::CONTEXT . '\Display\Preview';

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
