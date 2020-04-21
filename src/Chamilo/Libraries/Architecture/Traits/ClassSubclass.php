<?php
namespace Chamilo\Libraries\Architecture\Traits;

/**
 *
 * @package Chamilo\Libraries\Architecture\Traits
 */
trait ClassSubClass
{
    use ClassFile;

    /**
     * Checks if the class in the given file is subclass of the given super classes
     *
     * @param string $file
     * @param string[] $superClasses
     *
     * @return boolean
     */
    protected function check_if_class_in_file_is_subclass_of($file, array $superClasses)
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

    /**
     * Determines the package namespace depending on the namespace of the test class
     *
     * @return string
     */
    abstract protected function determine_package_namespace();
}