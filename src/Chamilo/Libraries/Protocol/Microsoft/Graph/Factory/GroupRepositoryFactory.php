<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Factory;

use Chamilo\Core\Admin\Service\Consulter\ConfigurationConsulter;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GroupRepository;

/**
 * Factory class for the GroupRepository
 *
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GroupRepositoryFactory
{
    /**
     * @var \Chamilo\Core\Admin\Service\Consulter\ConfigurationConsulter
     */
    protected $configurationConsulter;

    /**
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository
     */
    protected $graphRepository;

    /**
     * GroupRepositoryFactory constructor.
     *
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository $graphRepository
     * @param \Chamilo\Core\Admin\Service\Consulter\ConfigurationConsulter $configurationConsulter
     */
    public function __construct(
        GraphRepository $graphRepository, ConfigurationConsulter $configurationConsulter
    )
    {
        $this->graphRepository = $graphRepository;
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     * @return \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GroupRepository
     */
    public function buildGroupRepository()
    {
        $cosnicsPrefix = $this->configurationConsulter->getSetting(
            ['Chamilo\Libraries', 'microsoft_graph_cosnics_prefix']
        );

        return new GroupRepository($this->graphRepository, $cosnicsPrefix);
    }
}