<?php

namespace PixelCaffeine\ProductCatalog\Exception;

use PixelCaffeine\Admin\Exception\AEPCException;
use PixelCaffeine\ProductCatalog\Entity\ProductCatalog as Entity;

/**
 * Class for Feed specific exceptions
 */
class EntityException extends AEPCException {

    public static function doesNotExist( $id ) {
        return new self( sprintf( __( 'The product catalog "%s" does not exist.', 'pixel-caffeine' ), $id ), 1 );
    }

    public static function isAlreadyExisting( Entity $entity ) {
        return new self( sprintf( __( 'The product catalog "%s" already exists.', 'pixel-caffeine' ), $entity->getId() ), 2 );
    }

    public static function noEntityDefined() {
        return new self( __( 'No entity defined yet', 'pixel-caffeine' ), 3 );
    }

    public static function nameIsEmpty() {
        return new self( __( 'Please, give a name to the product catalog', 'pixel-caffeine' ), 4 );
    }

}
