<?php
/**
 * New Product Notification Email Template (Plain Text)
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

_e( 'New Product Submitted', 'wp-my-product-webspark' );
echo "\n\n";

printf( __( 'A new product has been submitted by %s.', 'wp-my-product-webspark' ), $object['author_name'] );
echo "\n\n";

_e( 'Product Details:', 'wp-my-product-webspark' );
echo "\n";

printf( __( 'Product Name: %s', 'wp-my-product-webspark' ), $object['product_name'] );
echo "\n";

printf( __( 'Author: %s', 'wp-my-product-webspark' ), $object['author_name'] );
echo "\n\n";

_e( 'Review Product:', 'wp-my-product-webspark' );
echo "\n";
echo $object['edit_product_link'];
echo "\n\n";

_e( 'Please review and take necessary actions.', 'wp-my-product-webspark' );
