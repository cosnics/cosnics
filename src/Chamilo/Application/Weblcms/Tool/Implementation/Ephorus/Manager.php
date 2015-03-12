<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus;

/**
 * This class represents the manager for the ephorus tool
 *
 * @author Pieterjan Broekaert - Hogeschool Gent
 * @author Tom Goethals - Hogeschool Gent
 * @author Vanpoucke Sven - Hogeschool Gent
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager
{
    const ACTION_PUBLISH_DOCUMENT = 'document_publisher';
    const ACTION_EPHORUS_REQUEST = 'ephorus_request';
    const ACTION_ASSIGNMENT_EPHORUS_REQUEST = 'assignment_ephorus_request';
    const ACTION_PUBLISH_LATEST_DOCUMENTS = 'assignment_latest_documents_publisher';
    const ACTION_ASSIGNMENT_BROWSER = 'assignment_browser';
    const ACTION_INDEX_VISIBILITY_CHANGER = 'index_visibility_changer';
    // const ACTION_HIDE_ON_INDEX = 'index_hider';
    const PARAM_CONTENT_OBJECT_IDS = 'co_ids';
    const PARAM_REQUEST_IDS = 'req_ids';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;
}
