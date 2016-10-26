<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\AssessmentMatchingQuestion\Storage\DataClass\AssessmentMatchingQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentMatchNumericQuestion\Storage\DataClass\AssessmentMatchNumericQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentMatchTextQuestion\Storage\DataClass\AssessmentMatchTextQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentMatrixQuestion\Storage\DataClass\AssessmentMatrixQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Storage\DataClass\AssessmentMultipleChoiceQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentOpenQuestion\Storage\DataClass\AssessmentOpenQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentRatingQuestion\Storage\DataClass\AssessmentRatingQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Storage\DataClass\AssessmentSelectQuestion;
use Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Storage\DataClass\FillInBlanksQuestion;
use Chamilo\Core\Repository\ContentObject\HotspotQuestion\Storage\DataClass\HotspotQuestion;
use Chamilo\Core\Repository\ContentObject\OrderingQuestion\Storage\DataClass\OrderingQuestion;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;

/**
 * $Id: complex_assessment.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.content_object.assessment
 */
/**
 * This class represents a complex assessment (used to create complex learning objects)
 */
class ComplexAssessment extends ComplexContentObjectItem
{

    public function get_allowed_types()
    {
        $allowed_types = array();
        $allowed_types[] = AssessmentRatingQuestion :: class_name();
        $allowed_types[] = AssessmentOpenQuestion :: class_name();
        $allowed_types[] = HotspotQuestion :: class_name();
        $allowed_types[] = FillInBlanksQuestion :: class_name();
        $allowed_types[] = AssessmentMultipleChoiceQuestion :: class_name();
        $allowed_types[] = AssessmentMatchingQuestion :: class_name();
        $allowed_types[] = AssessmentSelectQuestion :: class_name();
        $allowed_types[] = AssessmentMatrixQuestion :: class_name();
        $allowed_types[] = AssessmentMatchNumericQuestion :: class_name();
        $allowed_types[] = AssessmentMatchTextQuestion :: class_name();
        $allowed_types[] = OrderingQuestion :: class_name();
        return $allowed_types;
    }
}
