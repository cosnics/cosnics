<?php
namespace Chamilo\Core\Repository\Common\Export\Zip;

use Chamilo\Core\Repository\Common\Export\ContentObjectExport;
use Chamilo\Core\Repository\Common\Export\ContentObjectExportController;
use Chamilo\Core\Repository\Common\Export\ContentObjectExportImplementation;
use Chamilo\Core\Repository\Common\Export\ExportParameters;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\File\Compression\ZipArchive\ZipArchiveFilecompression;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

class ZipContentObjectExportController extends ContentObjectExportController
{

    /**
     * @var string
     */
    private $temporary_directory;

    public function __construct(ExportParameters $parameters)
    {
        parent::__construct($parameters);

        $this->prepare_file_system();
    }

    public function run()
    {
        $content_object_ids = $this->get_parameters()->get_content_object_ids();
        if (count($content_object_ids) > 0)
        {
            $condition = new InCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID), $content_object_ids
            );
        }
        else
        {
            $condition = null;
        }

        $parameters = new DataClassRetrievesParameters($condition);
        $content_objects = DataManager::retrieve_active_content_objects(ContentObject::class, $parameters);

        foreach ($content_objects as $content_object)
        {
            $this->process($content_object);
        }

        return $this->zip();
    }

    /**
     * @param $source      string
     * @param $destination string
     */
    public function add_files($source, $destination)
    {
        $result = Filesystem::recurse_copy($source, $this->temporary_directory . $destination, true);
    }

    protected function getZipArchiveFilecompression(): ZipArchiveFilecompression
    {
        return $this->getService(ZipArchiveFilecompression::class);
    }

    public function get_filename()
    {
        return 'content_objects.zip';
    }

    public function get_temporary_directory()
    {
        return $this->temporary_directory;
    }

    public function prepare_file_system()
    {
        $user_id = $this->getSessionUtilities()->getUserId();

        $this->temporary_directory =
            $this->getConfigurablePathBuilder()->getTemporaryPath(__NAMESPACE__) . $user_id . DIRECTORY_SEPARATOR .
            'export_content_objects' . DIRECTORY_SEPARATOR;

        if (!is_dir($this->temporary_directory))
        {
            mkdir($this->temporary_directory, 0777, true);
        }
    }

    public function process($content_object)
    {
        $export_types = ContentObjectExportImplementation::get_types_for_object($content_object::CONTEXT);

        if (in_array(ContentObjectExport::FORMAT_ZIP, $export_types))
        {
            ContentObjectExportImplementation::launch(
                $this, $content_object, ContentObjectExport::FORMAT_ZIP, $this->get_parameters()->getType()
            );
        }
    }

    public function zip()
    {
        $zip = $this->getZipArchiveFilecompression();
        $zip_path = $zip->createArchive($this->temporary_directory);
        Filesystem::remove($this->temporary_directory);

        return $zip_path;
    }
}
