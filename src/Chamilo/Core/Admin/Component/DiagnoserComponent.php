<?php
namespace Chamilo\Core\Admin\Component;

use Chamilo\Core\Admin\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Support\Diagnoser;

/**
 * @package Chamilo\Core\Admin\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DiagnoserComponent extends Manager
{

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function run()
    {
        if (!$this->getUser() instanceof User || !$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->getDiagnoser()->render();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    protected function getDiagnoser(): Diagnoser
    {
        return $this->getService(Diagnoser::class);
    }
}
