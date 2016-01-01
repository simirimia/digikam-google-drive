<?php
declare(strict_types=1);
namespace Simirimia\DigiKam;

use PDO;

class DigiKam
{

    /**
     * @var string
     */
    private $digiKamDPath;

    /**
     * @var string
     */
    private $myDbPath;

    public function __construct( string $myDbPath, string $digiKamDbPath )
    {
        $this->digiKamDPath = $digiKamDbPath;
        $this->myDbPath = $myDbPath;
    }

    public function getPhotos() :PhotoCollection
    {
        $collection = new PhotoCollection();

        $data = $this->getData();

        $photo = null;
        foreach( $data as $current ) {

            if ( $current instanceof \stdClass ) {
                /** @var \stdClass $current */

                if ($photo instanceof Photo) {
                    if ($photo->getId() == $current->id) {
                        $photo->addTag(implode('/', [$current->tag1, $current->tag2, $current->tag3]));
                    } else {
                        $photo = Photo::createFromObject($current);
                    }
                } else {
                    $photo = Photo::createFromObject($current);
                }

            } elseif ( is_array( $current ) ) {
                /** @var array $current */

                if ($photo instanceof Photo) {
                    if ($photo->getId() == $current['id']) {
                        $photo->addTag(implode('/', [$current['tag1'], $current['tag2'], $current['tag3']]));
                    } else {
                        $photo = Photo::createFromArray($current);
                    }
                } else {
                    $photo = Photo::createFromArray($current);
                }

            }

            $collection->add( $photo );
        }

        return $collection;
    }

    private function getData() :array
    {
        //return $this->getTestData();
        $data = $this->getNewDigiKamPhotos();

        //var_dump($data);
        //exit;
        return $data;
    }

    private function getNewDigiKamPhotos() :array
    {
        $myDb = new PDO( 'sqlite:' .$this->myDbPath );
        $myDb->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $statement = $myDb->prepare( 'SELECT digikamId FROM photos' );
        $statement->execute();

        $ids = [];
        while($val = $statement->fetchColumn()) $ids[] = $val;
        $inQuery = implode( ',', array_fill(0, count( $ids ), '?' ));


        $digiKamDb = new PDO( 'sqlite:' . $this->digiKamDPath );
        $digiKamDb->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

        $sql = <<<sql
SELECT i.id, ar.specificPath, a.relativePath, i.name, t.name AS tag1, t2.name AS tag2, t3.name AS tag3
FROM Images i
JOIN Albums a ON i.album=a.id
JOIN AlbumRoots ar ON ar.id=a.albumRoot
LEFT OUTER JOIN ImageTags it ON it.imageid=i.id
LEFT OUTER JOIN Tags t ON t.id=it.tagid
LEFT OUTER JOIN Tags t2 ON t.pid=t2.id
LEFT OUTER JOIN Tags t3 ON t2.pid=t3.id
WHERE ( t.id IS  NULL OR t.id NOT IN ( 1,6,16,48 ) )
 AND ar.id = 2
 AND i.id NOT IN ( {$inQuery} )
ORDER BY ar.specificPath,a.relativePath,i.name
sql;

        $statement = $digiKamDb->query( $sql );
        $statement->execute( $ids );
        $foo = $statement->fetchAll( PDO::FETCH_NAMED );

        return $foo;
    }

    private function getTestData() :array
    {
        return json_decode( file_get_contents( '/home/vagrant/code/digikam-google-photos/testdata/photos.json' ) );
    }

}