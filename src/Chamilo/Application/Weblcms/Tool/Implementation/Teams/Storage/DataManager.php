<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class DataManager
 * @inheritdoc
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    use ContainerAwareTrait;

    const PREFIX = 'weblcms_';
}