<?php

namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository;

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
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository
     */
    protected $graphRepository;

    /**
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    protected $configurationConsulter;

    /**
     * GroupRepositoryFactory constructor.
     *
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository $graphRepository
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function __construct(
        \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository $graphRepository,
        \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
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
            ['Chamilo\Libraries\Protocol\Microsoft\Graph', 'cosnics_prefix']
        );

        return new GroupRepository($this->graphRepository, $cosnicsPrefix);
    }
}