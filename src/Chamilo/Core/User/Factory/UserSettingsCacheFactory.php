<?php
namespace Chamilo\Core\User\Factory;

use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * @package Chamilo\Core\User\Factory
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserSettingsCacheFactory
{
    protected ConfigurablePathBuilder $configurablePathBuilder;

    /**
     * @param \Chamilo\Libraries\File\ConfigurablePathBuilder $configurablePathBuilder
     */
    public function __construct(ConfigurablePathBuilder $configurablePathBuilder)
    {
        $this->configurablePathBuilder = $configurablePathBuilder;
    }

    public function createUserSettingsCache(): FilesystemAdapter
    {
        return new FilesystemAdapter(
            'UserSetting', 0, $this->getConfigurablePathBuilder()->getCachePath('Chamilo\Core\User')
        );
    }

    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->configurablePathBuilder;
    }
}