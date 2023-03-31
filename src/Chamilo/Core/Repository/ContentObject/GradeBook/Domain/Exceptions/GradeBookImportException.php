<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Domain\Exceptions;

interface GradeBookImportException
{
    public function getProperties(): array;
}
