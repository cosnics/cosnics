<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Integration\Chamilo\Core\Reporting;

interface Answer
{

    function get_question_id();

    function get_option_id();

    function get_match_id();
}
?>