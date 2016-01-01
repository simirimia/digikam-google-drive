<?php
declare( strict_types = 1 );
require __DIR__ . '/../vendor/autoload.php';

define( 'CREDENTIALS_PATH', '~/.digikam-to-google-drive-sync-credentials.json' );
define( 'CLIENT_SECRET_PATH', __DIR__ . '/../config/app_secret_cli.json' );
define( 'DIGIKAM_DATABASE_PATH', __DIR__ . '/../testdata/digikam4.db' );
define( 'APPLICATION_DB_PATH', __DIR__ . '/../testdata/digiGoogle.db' );

use Simirimia\DigiKam\DigiKam;
use Simirimia\DigiKam\Photo;
use Simirimia\DigiKam\PhotoRepository;
use Simirimia\DigiKam\FolderConfig;
use Simirimia\DigiKam\DigiKamDriveFactory;

$config = new FolderConfig( json_decode( file_get_contents( __DIR__ . '/../config/folders.json' )) );;
$factory = new DigiKamDriveFactory( CREDENTIALS_PATH, CLIENT_SECRET_PATH, $config );
$digiDrive = $factory->create();

$digikam = new DigiKam( APPLICATION_DB_PATH, DIGIKAM_DATABASE_PATH );
$photos = $digikam->getPhotos();

$repository = new PhotoRepository( APPLICATION_DB_PATH );

/** @var Photo $photo */
foreach( $photos as $photo ) {
    printf( "Adding %s%s \n", $photo->getRelativePath(), $photo->getLocalFileName() );

    $file = $digiDrive->add( $photo );

    if ( $file->getId() == null ) {
        continue;
    }

    $photo->setGoogleId( $file->getId() );
    $photo->setCurrentRevision( $file->getHeadRevisionId() );
    $photo->setGoogleUrl( $file->getAlternateLink() );

    $repository->store( $photo );
}

