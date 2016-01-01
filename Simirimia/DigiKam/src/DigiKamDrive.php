<?php
declare(strict_types=1);
namespace Simirimia\DigiKam;

use Simirimia\GoogleDrive\GoogleDrive;
use Google_Service_Drive_DriveFile;

class DigiKamDrive
{
    /**
     * @var GoogleDrive
     */
    private $drive;

    /**
     * @var FolderConfig
     */
    private $folderConfig;

    public function __construct( GoogleDrive $googleDrive, FolderConfig $folderConfig )
    {
        $this->drive = $googleDrive;
        $this->folderConfig = $folderConfig;
    }

    public function add( Photo $photo ): Google_Service_Drive_DriveFile
    {
        $googleId = $this->folderConfig->getGoogleId( $photo->getSpecificPath() . $photo->getRelativePath() );

        if ( $googleId === '' ) {
            $googleId = $this->folderConfig->getGoogleId( $photo->getSpecificPath() );
            if ( $googleId === '' ) {
                throw new \Exception( sprintf( "Invalid configuration. DigiKam Drive Root Folder must be set. Folder Info given was %s \n",
                    $photo->getSpecificPath() ));
            }
            $googleId = $this->drive->createFolderStructure( $googleId, $photo->getRelativePath() );
            $this->folderConfig->add( $photo->getSpecificPath() . $photo->getRelativePath(), $googleId );
        }

        return $this->drive->add( $photo, $googleId );
    }

}