<?php
declare(strict_types=1);
namespace Simirimia\DigiKam;

use \PDO;

class PhotoRepository
{
    /**
     * @var string
     */
    private $dbPath;

    public function __construct( string $dbPath )
    {
        $this->dbPath = $dbPath;
    }

    public function store( Photo $photo )
    {
        $db = new PDO( 'sqlite:' . $this->dbPath );
        $db->setAttribute(\PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $db->exec( 'CREATE TABLE IF NOT EXISTS photos (
                        digikamId INTEGER PRIMARY KEY,
                        googleId TEXT,
                        googleUrl TEXT,
                        currentRevision TEXT
)' );

        $sql = 'REPLACE INTO photos
                               (
                                  digikamId,   googleId,  googleUrl,  currentRevision )
                        VALUES (
                                  :digikamId, :googleId, :googleUrl, :currentRevision
                                  )';

        $statement = $db->prepare(  $sql );
        $id = $photo->getId();
        $statement->bindParam( ':digikamId', $id, PDO::PARAM_INT );
        $gid = $photo->getGoogleId();
        $statement->bindParam( ':googleId', $gid, PDO::PARAM_STR );
        $val = $photo->getGoogleUrl();
        $statement->bindParam( ':googleUrl', $val, PDO::PARAM_STR );
        $val = $photo->getCurrentRevision();
        $statement->bindParam( ':currentRevision', $val , PDO::PARAM_STR );
        $result = $statement->execute();

        if ( !$result ) {
            throw new \Exception( 'REPLACE statement returned false' );
        }
    }
}