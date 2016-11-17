<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus;

use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;

/**
 * This class represents the manager for the ephorus tool
 * 
 * @author Pieterjan Broekaert - Hogeschool Gent
 * @author Tom Goethals - Hogeschool Gent
 * @author Vanpoucke Sven - Hogeschool Gent
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager implements IntroductionTextSupportInterface
{
    const ACTION_PUBLISH_DOCUMENT = 'DocumentPublisher';
    const ACTION_EPHORUS_REQUEST = 'EphorusRequest';
    const ACTION_ASSIGNMENT_EPHORUS_REQUEST = 'AssignmentEphorusRequest';
    const ACTION_PUBLISH_LATEST_DOCUMENTS = 'AssignmentLatestDocumentsPublisher';
    const ACTION_ASSIGNMENT_BROWSER = 'AssignmentBrowser';
    const ACTION_INDEX_VISIBILITY_CHANGER = 'IndexVisibilityChanger';
    // const ACTION_HIDE_ON_INDEX = 'IndexHider';
    const PARAM_CONTENT_OBJECT_IDS = 'co_ids';
    const PARAM_REQUEST_IDS = 'req_ids';
    const DEFAULT_ACTION = self::ACTION_BROWSE;
}
