<?php
namespace Chamilo\Configuration\Package\Properties\Dependencies\Dependency;

use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @author Hans De Bisschop
 * @package core.lynx.package
 */
class Version
{
    const OPERATOR_EQUAL = 1;
    const OPERATOR_NOT_EQUAL = 2;
    const OPERATOR_GREATER_THEN = 3;
    const OPERATOR_GREATER_THEN_OR_EQUAL = 4;
    const OPERATOR_LESS_THEN = 5;
    const OPERATOR_LESS_THEN_OR_EQUAL = 6;

    /**
     *
     * @var string
     */
    private $release;

    /**
     *
     * @var int
     */
    private $operator;

    /**
     *
     * @param string $release
     * @param string $operator
     */
    public function __construct($release, $operator)
    {
        $this->set_release($release);
        $this->set_operator($operator);
    }

    /**
     *
     * @return string
     */
    public function get_release()
    {
        return $this->release;
    }

    /**
     *
     * @param string $release
     */
    public function set_release($release)
    {
        $this->release = $release;
    }

    /**
     *
     * @return int
     */
    public function get_operator()
    {
        return $this->operator;
    }

    /**
     *
     * @param int $operator
     */
    public function set_operator($operator)
    {
        $this->operator = $operator;
    }

    public function get_operator_name()
    {
        return self::operator_name($this->get_operator());
    }

    /**
     *
     * @param int $operator
     * @return string
     */
    public static function operator_name($operator)
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
     *
     * @param int $type
     * @param string $reference
     * @param string $value
     * @return boolean
     */
    public static function compare($type, $reference, $value)
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
}
