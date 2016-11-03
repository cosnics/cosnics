<?php

namespace Chamilo\Core\Repository\Implementation\GoogleDocs\Infrastructure\Service;

use Chamilo\Libraries\File\FileType;

class MimeTypeExtensionParser
{
    /**
     * Maps the google drive export mimetypes explicitly to a chosen extension to avoid the problem with multiple
     * extensions for the same mimetype and visa verca
     *
     * @var array
     */
    protected $mimeTypeExtensionMapping = array(
        'text/html' => 'htm',
        'text/plain' => 'txt',
        'application/rtf' => 'rtf',
        'application/vnd.oasis.opendocument.text' => 'odt',
        'application/pdf' => 'pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'application/x-vnd.oasis.opendocument.spreadsheet' => 'ods',
        'text/csv' => 'csv',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/svg+xml' => 'svg',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
        'application/vnd.google-apps.script+json' => 'json'
    );

    /**
     * Returns a valid extension for the given mimetype
     *
     * @param string $mimeType
     *
     * @return string
     */
    public function getExtensionForMimeType($mimeType)
    {
        if(array_key_exists($mimeType, $this->mimeTypeExtensionMapping))
        {
            return $this->mimeTypeExtensionMapping[$mimeType];
        }

        $possibleExtensions = FileType::get_extensions($mimeType);

        return array_shift($possibleExtensions);
    }

    /**
     * Returns a valid extension for the given mimetype
     *
     * @param string $extension
     *
     * @return string
     */
    public function getMimeTypeForExtension($extension)
    {
        $mimeType = array_search($extension, $this->mimeTypeExtensionMapping);
        if($mimeType)
        {
            return $mimeType;
        }

        return FileType::get_mimetype($extension);
    }
}