<?php
namespace Chamilo\Core\Admin\Component;

use Chamilo\Core\Admin\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Diagnoser\Diagnoser;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Admin\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DiagnoseComponent extends Manager
{
    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     */
    public function run(?User $currentUser = null): Response
    {
        if (!$currentUser instanceof User || !$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $html = [];

        $html[] = $this->renderHeader($currentUser);
        $html[] = $this->getDiagnoser()->render();
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }

    protected function getDiagnoser(): Diagnoser
    {
        return $this->getService(Diagnoser::class);
    }
}
