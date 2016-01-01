<?php
declare(strict_types=1);
namespace Simirimia\DigiKam;

class FolderConfig
{
    /**
     * @var array
     */
    private $mapping = [];

    public function __construct( \stdClass $folderConfig )
    {
        foreach( $folderConfig->DigiKamAlbumbRoots as $root ) {
            $this->mapping[$root->localPath] = $root->googleFolderId;
        }
    }

    public function getGoogleId( string $folder ) :string
    {
        return $this->mapping[$folder] ?? '';
    }

    public function add( string $localPath, string $googleId )
    {
        $this->mapping[$localPath] = $googleId;
    }
}