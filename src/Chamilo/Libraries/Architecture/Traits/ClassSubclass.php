<?php
namespace Chamilo\Libraries\Architecture\Traits;

/**
 *
 * @package Chamilo\Libraries\Architecture\Traits
 */
trait ClassSubClass
{
    use ClassFile;

    protected function checkIfClassInFileIsSubclassOf(string $file, array $superClasses): bool
    {
        if (empty($file))
        {
            return false;
        }

        $className = $this->getClassNameFromPHPFile($file);

        foreach ($superClasses as $superClass)
        {
            if (is_subclass_of($className, $superClass))
            {
                return true;
            }
        }

        return false;
    }

    abstract protected function determinePackageNamespace(): string;
}