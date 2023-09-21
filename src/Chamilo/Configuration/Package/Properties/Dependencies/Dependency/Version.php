<?php
namespace Chamilo\Configuration\Package\Properties\Dependencies\Dependency;

use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Configuration\Package\Properties\Dependencies\Dependency
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Version
{
    public const OPERATOR_EQUAL = 1;
    public const OPERATOR_GREATER_THEN = 3;
    public const OPERATOR_GREATER_THEN_OR_EQUAL = 4;
    public const OPERATOR_LESS_THEN = 5;
    public const OPERATOR_LESS_THEN_OR_EQUAL = 6;
    public const OPERATOR_NOT_EQUAL = 2;

    private int $operator;

    private string $release;

    public function __construct(string $release, int $operator)
    {
        $this->set_release($release);
        $this->set_operator($operator);
    }

    public static function compare(int $type, string $reference, string $value): bool
    {
        switch ($type)
        {
            case self::OPERATOR_EQUAL :
                return version_compare($reference, $value, '==');
                break;
            case self::OPERATOR_NOT_EQUAL :
                return version_compare($reference, $value, '!=');
                break;
            case self::OPERATOR_GREATER_THEN :
                return version_compare($value, $reference, '>');
                break;
            case self::OPERATOR_GREATER_THEN_OR_EQUAL :
                return version_compare($value, $reference, '>=');
                break;
            case self::OPERATOR_LESS_THEN :
                return version_compare($value, $reference, '<');
                break;
            case self::OPERATOR_LESS_THEN_OR_EQUAL :
                return version_compare($value, $reference, '<=');
                break;
            default :
                return false;
                break;
        }
    }

    /**
     * @return int
     */
    public function get_operator(): int
    {
        return $this->operator;
    }

    public function get_operator_name(): string
    {
        return self::operator_name($this->get_operator());
    }

    /**
     * @return string
     */
    public function get_release(): string
    {
        return $this->release;
    }

    public static function operator_name(int $operator): string
    {
        switch ($operator)
        {
            case self::OPERATOR_EQUAL :
                return Translation::get('ShortEqual');
                break;
            case self::OPERATOR_NOT_EQUAL :
                return Translation::get('ShortNotEqual');
                break;
            case self::OPERATOR_GREATER_THEN :
                return Translation::get('ShortGreater');
                break;
            case self::OPERATOR_GREATER_THEN_OR_EQUAL :
                return Translation::get('ShortGreaterThenOrEqual');
                break;
            case self::OPERATOR_LESS_THEN :
                return Translation::get('ShortLessThen');
                break;
            case self::OPERATOR_LESS_THEN_OR_EQUAL :
                return Translation::get('ShortLessThenOrEqual');
                break;
        }
    }

    /**
     * @param int $operator
     */
    public function set_operator($operator)
    {
        $this->operator = $operator;
    }

    /**
     * @param string $release
     */
    public function set_release($release)
    {
        $this->release = $release;
    }
}
