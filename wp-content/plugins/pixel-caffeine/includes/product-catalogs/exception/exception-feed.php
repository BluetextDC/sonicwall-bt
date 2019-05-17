<?php

namespace PixelCaffeine\ProductCatalog\Exception;
use PixelCaffeine\Admin\Exception\AEPCException;
use PixelCaffeine\ProductCatalog\FeedMapper;

/**
 * Class for Feed specific exceptions
 */
class FeedException extends AEPCException {

    public static function formatNotSupported( $format ) {
        return new self( sprintf( __( 'The format "%s" for the feed is not supported yet.', 'pixel-caffeine' ), $format ), 1 );
    }

    public static function writerNotInitialized( $format ) {
        return new self( sprintf( __( 'The format "%s" is not initialized for object writing.', 'pixel-caffeine' ), $format ), 2 );
    }

    public static function weightUnitNotSupported( $unit, \AEPC_Addon_Product_Item $item ) {
        return new self( sprintf( __( '%sProduct #%s%s error: the weight unit "%s" in product feed is not supported by Facebook.', 'pixel-caffeine' ), '<a href="' . $item->get_admin_url() . '">', $item->get_id(), '</a>', $unit ), 3 );
    }

    public static function mandatoryField( $field, \AEPC_Addon_Product_Item $item ) {
        if ( $item->is_variation() ) {
	        return new self(sprintf(__('Variation #%s of %sproduct #%s%s error: the field "%s" in must not be empty.',
		        'pixel-caffeine'), $item->get_id(), '<a href="' . $item->get_parent_admin_url() . '">', $item->get_group_id(),
		        '</a>', $field), 4);
        } else {
	        return new self(sprintf(__('%sProduct #%s%s error: the field "%s" in must not be empty.', 'pixel-caffeine'),
		        '<a href="' . $item->get_admin_url() . '">', $item->get_id(), '</a>', $field), 4);
        }
    }

    public static function googleCategoryMandatory( \AEPC_Addon_Product_Item $item ) {
        return new self( sprintf( __( '%sProduct #%s%s error: a google product category must be defined in the product or at least in the product catalog configuration.', 'pixel-caffeine' ), '<a href="' . $item->get_admin_url() . '">', $item->get_id(), '</a>' ), 5 );
    }

    public static function itemDoesNotExist( FeedMapper $item ) {
        return new self( sprintf( __( 'EDIT ERROR: The item %s"%s (#%s)"%s does not exist inside the product feed', 'pixel-caffeine' ), '<a href="' . $item->get_permalink() . '">', $item->get_title(), $item->get_id(), '</a>' ), 6 );
    }

    public static function noBackupVersionOfFeed() {
        return new self( __( 'SAVING ERROR: There is no backup version of the feed to restore', 'pixel-caffeine' ), 7 );
    }

    public static function feedDoesNotExist() {
        return new self( __( 'There is not feed file to backup', 'pixel-caffeine' ), 8 );
    }

    public static function feedCannotBeSaved( \WP_Error $wp_error ) {
        return new self( sprintf( __( 'The saving process cannot be started: %s', 'pixel-caffeine' ), $wp_error->get_error_message() ), 9 );
    }

}
