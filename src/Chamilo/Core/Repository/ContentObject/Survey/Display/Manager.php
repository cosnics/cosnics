<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Display;

abstract class Manager extends \Chamilo\Core\Repository\Display\Manager
{
    const PARAM_AJAX_CONTEXT = 'ajax_context';
    const PARAM_CONTENT_OBJECT_ID = 'content_object_id';
    const PARAM_ROOT_CONTENT_OBJECT_ID = 'root_content_object_id';
    const PARAM_STEP = 'step';
}
?>