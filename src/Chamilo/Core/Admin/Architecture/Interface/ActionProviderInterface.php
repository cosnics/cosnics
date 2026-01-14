<?php
namespace Chamilo\Core\Admin\Architecture\Interface;

use Chamilo\Libraries\Format\Tabs\Actions;

/**
 * @package Chamilo\Core\Admin\Service
 */
interface ActionProviderInterface
{

    public function getActions(): Actions;

    public function getContext(): string;
}