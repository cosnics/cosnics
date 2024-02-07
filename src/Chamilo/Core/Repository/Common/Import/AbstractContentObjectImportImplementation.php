<?php
namespace Chamilo\Core\Repository\Common\Import;

/**
 *
 * @package repository.lib
 *          A class to export a ContentObject.
 */
abstract class AbstractContentObjectImportImplementation
{

    private ContentObjectImportParameters $content_object_import_parameters;

    private ContentObjectImportController $controller;

    public function __construct(
        ContentObjectImportController $controller, ContentObjectImportParameters $content_object_import_parameters
    )
    {
        $this->controller = $controller;
        $this->content_object_import_parameters = $content_object_import_parameters;
    }

    public function get_content_object_import_parameters(): ContentObjectImportParameters
    {
        return $this->content_object_import_parameters;
    }

    public function set_content_object_import_parameters(ContentObjectImportParameters $content_object_import_parameters
    ): void
    {
        $this->content_object_import_parameters = $content_object_import_parameters;
    }

    public function get_controller(): ContentObjectImportController
    {
        return $this->controller;
    }

    public function set_controller(ContentObjectImportController $controller): void
    {
        $this->controller = $controller;
    }
}
