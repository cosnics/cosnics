<?php
namespace Chamilo\Libraries\Protocol\Mail\Architecture\Domain;

/**
 * Describes a file that can be embedded inline or attached to an e-mail.
 *
 * @package Chamilo\Libraries\Protocol\Mail\Architecture\Domain
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class MailFile
{
    private string $filename;

    private ?string $mimeType;

    private string $path;

    public function __construct(string $filename, string $path, ?string $mimeType = null)
    {
        $this->filename = $filename;
        $this->path = $path;
        $this->mimeType = $mimeType;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}