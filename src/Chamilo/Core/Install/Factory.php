<?php
namespace Chamilo\Core\Install;

use Chamilo\Core\Install\Storage\DataManager;

class Factory
{

    public function build_installer(Configuration $config)
    {
        $data_manager = $this->build_data_manager($config);
        return new PlatformInstaller($config, $data_manager);
    }

    public function build_config_from_array(array $values)
    {
        $res = new Configuration();
        $res->load_array($values);
        return $res;
    }

    public function build_installer_from_array(array $values)
    {
        $config = $this->build_config_from_array($values);
        return $this->build_installer($config);
    }

    public function build_data_manager(Configuration $config)
    {
        return new DataManager($config);
    }
}
