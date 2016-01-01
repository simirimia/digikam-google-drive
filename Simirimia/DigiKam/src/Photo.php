<?php
declare(strict_types=1);
namespace Simirimia\DigiKam;

use Simirimia\GoogleDrive\File;

class Photo implements File
{

    const SEPARATOR = '§§--dkgp--§§';

    private $id;
    private $specificPath;
    private $relativePath;
    private $name;
    private $tags = [];

    // keep external description
    private $preDescription = '';
    private $postDescription = '';

    // Google Drive Data
    private $googleUrl = '';
    private $googleId = '';
    private $currentRevision = '';

    public static function createFromArray( array $data ) :Photo
    {
        $photo = new static();
        $photo->setId( (int)$data['id'] );
        $photo->setSpecificPath( $data['specificPath'] );
        $photo->setRelativePath( $data['relativePath'] );
        $photo->setName( $data['name'] );
        $photo->addTag( implode( '/', [ $data['tag1'], $data['tag2'], $data['tag3'] ] ) );
        return $photo;
    }

    public static function createFromObject( \stdClass $data ) :Photo
    {
        $photo = new static();
        $photo->setId( (int)$data->id );
        $photo->setSpecificPath( $data->specificPath );
        $photo->setRelativePath( $data->relativePath );
        $photo->setName( $data->name );
        $photo->addTag( implode( '/', [ $data->tag1, $data->tag2, $data->tag3 ] ) );
        return $photo;
    }

    public function getId() :int
    {
        return $this->id;
    }

    public function getTitle() :string
    {
        return $this->name;
    }

    public function getDescription() :string
    {
        return  implode( "\n --- ", array_merge([$this->preDescription, self::SEPARATOR, "DigiKam Tags: "],
            $this->tags, [self::SEPARATOR, $this->getId(), self::SEPARATOR, $this->postDescription] ));
    }

    public function getMimeType() :string
    {
        return 'image/jpeg';
    }

    public function getLocalFileName() :string
    {
        return $this->name ;
    }

    public function getLocalPath() :string
    {
        return $this->specificPath . $this->relativePath . DIRECTORY_SEPARATOR;
    }

    public function setId( int $id )
    {
        $this->id = $id;
    }

    public function addTag( string $tag )
    {
        $this->tags[] = $tag;
    }

    public function getSpecificPath() :string
    {
        return $this->specificPath;
    }

    public function getRelativePath() :string
    {
        return $this->relativePath;
    }


    public function setSpecificPath( string $specificPath )
    {
        $this->specificPath = $specificPath;
    }

    public function setRelativePath( string $relativePath )
    {
        $this->relativePath = $relativePath;
    }

    public function setName( string $name )
    {
        $this->name = $name;
    }

    public function getGoogleId() :string
    {
        return $this->googleId;
    }

    public function setGoogleId( string $id )
    {
        $this->googleId = $id;
    }

    public function getGoogleUrl() :string
    {
        return $this->googleUrl;
    }

    public function setGoogleUrl( string $url )
    {
        $this->googleUrl = $url;
    }

    public function getCurrentRevision() :string
    {
        return $this->currentRevision;
    }

    public function setCurrentRevision( string $revision )
    {
        $this->currentRevision = $revision;
    }


}