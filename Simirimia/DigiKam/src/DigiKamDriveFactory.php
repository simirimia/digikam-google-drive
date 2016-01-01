<?php
declare(strict_types=1);
namespace Simirimia\DigiKam;

use Simirimia\GoogleDrive\CredentialsRepositoryFile;
use Simirimia\GoogleDrive\GoogleDrive;
use Simirimia\GoogleDrive\GoogleClientFactory;
use Simirimia\GoogleDrive\InteractionHandlerCli;
use Google_Service_Drive;

class DigiKamDriveFactory
{
    /**
     * @var string
     */
    private $credentialsPath;
    /**
     * @var string
     */
    private $clientSecretPath;
    /**
     * @var FolderConfig
     */
    private $config;

    public function __construct( string $credentialsPath, string $clientSecretPath, FolderConfig $config  )
    {
        $this->credentialsPath = $credentialsPath;
        $this->clientSecretPath = $clientSecretPath;
        $this->config = $config;
    }

    public function create() : DigiKamDrive
    {

        $credentialsRepository = new CredentialsRepositoryFile( $this->credentialsPath );
        $interactionHandler = new InteractionHandlerCli();

        $factory = new GoogleClientFactory(
            'DigiKam to Google Drive Sync',
            Google_Service_Drive::DRIVE,
            $this->clientSecretPath,
            $credentialsRepository,
            $interactionHandler );


        $googleClient = $factory->create();
        $drive = new GoogleDrive( $googleClient );

        return new DigiKamDrive( $drive, $this->config );
    }
}