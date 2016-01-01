<?php
namespace Simirimia\DigiKam;

use Simirimia\Core\ArrayCollection;

class PhotoCollection extends ArrayCollection
{
    public function __construct()
    {
        parent::__construct( '\Simirimia\DigiKam\Photo', 'getId' );
    }

    /**
     * @param \Simirimia\DigiKam\Photo $element
     */
    public function add( $element )
    {
        parent::add( $element );
    }
}