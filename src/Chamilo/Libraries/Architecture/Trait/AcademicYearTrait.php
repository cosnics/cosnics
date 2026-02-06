<?php
namespace Chamilo\Libraries\Architecture\Trait;

/**
 * @package Chamilo\Libraries\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait AcademicYearTrait
{
    /**
     * @var string[]
     */
    protected array $academicYears;

    /**
     * @return string[]
     */
    protected function getAcademicYears(): array
    {
        return $this->academicYears;
    }

    protected function setAcademicYears(array $academicYears): static
    {
        $this->academicYears = $academicYears;

        return $this;
    }
}