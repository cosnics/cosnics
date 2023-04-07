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
 *
 * @package repository.lib.content_object.assessment
 */
/**
 * This class represents a complex assessment (used to create complex learning objects)
 */
class ComplexAssessment extends ComplexContentObjectItem
{

    public function get_allowed_types(): array
    {
        $allowed_types = [];
        $allowed_types[] = AssessmentRatingQuestion::class;
        $allowed_types[] = AssessmentOpenQuestion::class;
        $allowed_types[] = HotspotQuestion::class;
        $allowed_types[] = FillInBlanksQuestion::class;
        $allowed_types[] = AssessmentMultipleChoiceQuestion::class;
        $allowed_types[] = AssessmentMatchingQuestion::class;
        $allowed_types[] = AssessmentSelectQuestion::class;
        $allowed_types[] = AssessmentMatrixQuestion::class;
        $allowed_types[] = AssessmentMatchNumericQuestion::class;
        $allowed_types[] = AssessmentMatchTextQuestion::class;
        $allowed_types[] = OrderingQuestion::class;
        return $allowed_types;
    }
}
