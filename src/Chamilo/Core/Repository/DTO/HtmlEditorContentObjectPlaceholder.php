<?php

namespace Chamilo\Core\Repository\DTO;

/**
 * Class HtmlEditorContentObjectPlaceholder
 *
 * Contains all the data necessary for the html editor to render the content object
 * @author pjbro <pjbro@users.noreply.github.com>
 */
class HtmlEditorContentObjectPlaceholder
{
    /**
     * @var string
     */
    protected $filename;

    /**
     * @var int
     */
    protected $contentObjectId;

    /**
     * @var string
     */
    protected $securityCode;

    /**
     * @var string
     *
     * short version of the content object type. e.g. file, section,...
     * However, image is a separate type as the rendering is different from file.
     */
    protected $type;

    /**
     * @var string
     */
    protected $thumbnailUrl;

    /**
     * HtmlEditorContentObjectPlaceholder constructor.
     * @param string $filename
     * @param int $contentObjectId
     * @param string $securityCode
     * @param string $type
     * @param string $thumbnailUrl
     */
    public function __construct($filename, $contentObjectId, $securityCode, $type, $thumbnailUrl)
    {
        $this->filename = $filename;
        $this->contentObjectId = $contentObjectId;
        $this->securityCode = $securityCode;
        $this->type = $type;
        $this->thumbnailUrl = $thumbnailUrl;
    }

    public function asArray()
    {
        return array(
            "filename" => $this->filename,
            "co-id" => $this->contentObjectId,
            "security-code" => $this->securityCode,
            "type" => $this->type,
            "url" => $this->thumbnailUrl
        );
    }
}