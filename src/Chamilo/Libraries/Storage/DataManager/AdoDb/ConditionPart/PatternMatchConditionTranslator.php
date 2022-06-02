<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart;

use Chamilo\Libraries\Storage\DataManager\AdoDb\Service\ConditionPartTranslatorService;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataClassDatabaseInterface;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\ConditionTranslator;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\AdoDb\ConditionPart
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class PatternMatchConditionTranslator extends ConditionTranslator
{

    public function processPattern(string $pattern): string
    {
        // Escape SQL wildcard characters, thus prefixing %, ', \ and _ with a backslash
        $pattern = preg_replace(['/\\\\/', '/%/', '/\'/', '/_/'], ['\\\\\\\\', '\%', '\\\'', '\_'], $pattern);

        // Replace asterisks and question marks that are not prefixed with a backslash with the SQL equivalent
        return preg_replace(['/(?<!\\\\)\*/', '/(?<!\\\\)\?/'], ['%', '_'], $pattern);
    }

    public function translate(
        ConditionPartTranslatorService $conditionPartTranslatorService, DataClassDatabaseInterface $dataClassDatabase,
        PatternMatchCondition $patternMatchCondition, ?bool $enableAliasing = true
    ): string
    {
        return $conditionPartTranslatorService->translate(
                $dataClassDatabase, $patternMatchCondition->getConditionVariable(), $enableAliasing
            ) . ' LIKE ' . $dataClassDatabase->quote($this->processPattern($patternMatchCondition->getPattern()));
    }
}
