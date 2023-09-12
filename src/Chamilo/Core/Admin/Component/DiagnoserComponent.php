<?php
namespace Chamilo\Core\Admin\Component;

use Chamilo\Core\Admin\Manager;
use Chamilo\Core\Admin\Service\BreadcrumbGenerator;
use Chamilo\Libraries\Format\Structure\BreadcrumbGeneratorInterface;
use Chamilo\Libraries\Support\Diagnoser;

/**
 * @package Chamilo\Core\Admin\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DiagnoserComponent extends Manager
{

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageChamilo');

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

    public function getBreadcrumbGenerator(): BreadcrumbGeneratorInterface
    {
        return $this->getService(BreadcrumbGenerator::class);
    }
}
