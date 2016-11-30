<?php
namespace Chamilo\Libraries\Architecture\Traits;

trait ClassSubClass
{
    use ClassFile;

    /**
     * Determines the package namespace depending on the namespace of the test class
     * 
     * @return string
     */
    abstract protected function determine_package_namespace();

    /**
     * Checks if the class in the given file is subclass of the given super classes
     * 
     * @param string $file
     * @param array $super_classes
     *
     * @return bool
     */
    protected function check_if_class_in_file_is_subclass_of($file, array $super_classes)
    {
        if (empty($file))
        {
            return false;
        }
        
        $class_name = $this->getClassNameFromPHPFile($file);

        foreach ($super_classes as $super_class)
        {
            if (is_subclass_of($class_name, $super_class))
            {
                return true;
            }
        }
        
        return false;
    }
}