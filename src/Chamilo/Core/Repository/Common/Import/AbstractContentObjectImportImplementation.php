<?php
namespace Chamilo\Core\Repository\Common\Import;

/**
 *
 * @package repository.lib
 *          A class to export a ContentObject.
 */
abstract class AbstractContentObjectImportImplementation
{

    /**
     *
     * @var ContentObjectImportController
     */
    private $controller;

    /**
     *
     * @var ContentObjectImportParameters
     */
    private $content_object_import_parameters;

    /**
     *
     * @param ContentObjectImportController $controller
     * @param ContentObjectImportParameters $content_object_import_parameters
     */
    public function __construct(ContentObjectImportController $controller, 
        ContentObjectImportParameters $content_object_import_parameters)
    {
        $this->controller = $controller;
        $this->content_object_import_parameters = $content_object_import_parameters;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Common\Export\ContentObjectExportController
     */
    public function get_controller()
    {
        return $this->controller;
    }

    /**
     *
     * @param $controller
     */
    public function set_controller($controller)
    {
        $this->controller = $controller;
    }

    /**
     *
     * @return ContentObjectImportParameters
     */
    public function get_content_object_import_parameters()
    {
        return $this->content_object_import_parameters;
    }

    /**
     *
     * @param ContentObjectImportParameters $content_object_import_parameters
     */
    public function set_content_object_import_parameters($content_object_import_parameters)
    {
        $this->content_object_import_parameters = $content_object_import_parameters;
    }
}
