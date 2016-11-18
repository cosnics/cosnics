<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Description;

use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Core\Repository\ContentObject\Description\Storage\DataClass\Description;

/*
 * use core\repository\content_object\file\storage\data_class\File; use core\repository\content_object\page\Page; use
 * core\repository\content_object\webpage\Webpage;
 */

/**
 * $Id: description_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * 
 * @package application.lib.weblcms.tool.description
 */

/**
 * This tool allows a user to publish descriptions in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager implements IntroductionTextSupportInterface
{
    const ACTION_VIEW_DESCRIPTIONS = 'Viewer';

    public static function get_allowed_types()
    {
        return array(Description::class_name()/*, File :: class_name(), Page :: class_name(), Webpage :: class_name()*/);
    }
}
