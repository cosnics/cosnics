<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Common\Export;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Common\ExportImplementation;

/**
 *
 * @package repository.content_object.survey_matrix_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class CpoExportImplementation extends ExportImplementation
{
    const SURVEY_MATRIX_QUESTION_EXPORT = 'survey_matrix_question_export';
    const OPTIONS_NODE = 'options';
    const OPTION_NODE = 'option';
    const MATCHES_NODE = 'matches';
    const MATCH_NODE = 'match';
}