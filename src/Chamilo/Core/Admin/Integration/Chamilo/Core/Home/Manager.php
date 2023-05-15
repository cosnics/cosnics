<?php
namespace Chamilo\Core\Admin\Integration\Chamilo\Core\Home;

class Manager
{
    public const CONTEXT = __NAMESPACE__;

    public function getBlockTypes()
    {
        return ['Chamilo\Core\Admin\Integration\Chamilo\Core\Home\Type\PortalHome'];
    }
}