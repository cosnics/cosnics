<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Home;

class Manager
{
    public const CONTEXT = __NAMESPACE__;

    public function getBlockTypes()
    {
        return ['Chamilo\Core\User\Integration\Chamilo\Core\Home\Type\Login'];
    }
}