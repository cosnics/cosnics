<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Factory;

use Chamilo\Core\Admin\Service\Consulter\ConfigurationConsulter;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GroupRepository;

/**
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Factory
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GroupRepositoryFactory
{
    protected ConfigurationConsulter $configurationConsulter;

    protected GraphRepository $graphRepository;

    public function __construct(
        GraphRepository $graphRepository, ConfigurationConsulter $configurationConsulter
    )
    {
        $this->graphRepository = $graphRepository;
        $this->configurationConsulter = $configurationConsulter;
    }

    public function buildGroupRepository(): GroupRepository
    {
        $cosnicsPrefix = $this->configurationConsulter->getSetting(
            ['Chamilo\Libraries', 'microsoft_graph_cosnics_prefix']
        );

        return new GroupRepository($this->graphRepository, $cosnicsPrefix);
    }
}