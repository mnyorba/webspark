<?php
namespace WebSpark\MyProduct;

use WC_Product;
use WP_Query;

class MyProductHandler {
	use SingletonTrait;

	private $endpoints = array(
		'add-product'  => 'add-product',
		'edit-product' => 'edit-product',
		'my-products'  => 'my-products',
	);

	public function __construct() {
		add_filter( 'woocommerce_account_menu_items', array( $this, 'add_my_product_menu_items' ) );
		add_action( 'init', array( $this, 'add_endpoint_rewrite_rules' ) );
		add_action( 'woocommerce_account_add-product_endpoint', array( $this, 'add_product_page' ) );
		add_action( 'woocommerce_account_edit-product_endpoint', array( $this, 'edit_product_page' ) );
		add_action( 'woocommerce_account_my-products_endpoint', array( $this, 'my_products_page' ) );
		add_action( 'wp_ajax_webspark_save_product', array( $this, 'save_product' ) );
		add_action( 'wp_ajax_webspark_edit_product', array( $this, 'edit_product' ) );
		add_action( 'wp_ajax_webspark_delete_product', array( $this, 'delete_product' ) );
	}

	public function add_my_product_menu_items( $menu_items ) {
		$menu_items['add-product']  = __( 'Add Product', 'wp-my-product-webspark' );
		$menu_items['edit-product'] = __( 'Edit Product', 'wp-my-product-webspark' );
		$menu_items['my-products']  = __( 'My Products', 'wp-my-product-webspark' );
		return $menu_items;
	}

	public function add_endpoint_rewrite_rules() {
		add_rewrite_endpoint( $this->endpoints['add-product'], EP_ROOT | EP_PAGES );
		add_rewrite_endpoint( $this->endpoints['edit-product'], EP_ROOT | EP_PAGES );
		add_rewrite_endpoint( $this->endpoints['my-products'], EP_ROOT | EP_PAGES );
	}

	public function add_product_page() {
		// Render Add Product Form
		wc_get_template(
			'myaccount/add-product.php',
			array(
				'max_upload_size' => wp_max_upload_size(),
				'upload_nonce'    => wp_create_nonce( 'webspark_product_upload' ),
			),
			'',
			WEBSPARK_MY_PRODUCT_PATH . 'templates/'
		);
	}

	public function edit_product_page() {
			// Render Edit Product Form
		$product_id = get_query_var( 'product_id' );
		$product    = wc_get_product( $product_id );
		wc_get_template(
			'myaccount/edit-product.php',
			array(
				'product'         => $product,
				'max_upload_size' => wp_max_upload_size(),
				'edit_nonce'      => wp_create_nonce( 'webspark_product_edit' ),
			),
			'',
			WEBSPARK_MY_PRODUCT_PATH . 'templates/'
		);
	}

	public function my_products_page() {
		$paged    = max( 1, get_query_var( 'paged' ) );
		$per_page = 10;

		$args = array(
			'post_type'      => 'product',
			'author'         => get_current_user_id(),
			'posts_per_page' => $per_page,
			'paged'          => $paged,
		);

		$products_query = new WP_Query( $args );

		wc_get_template(
			'myaccount/my-products.php',
			array(
				'products_query' => $products_query,
				'paged'          => $paged,
				'total_pages'    => $products_query->max_num_pages,
			),
			'',
			WEBSPARK_MY_PRODUCT_PATH . 'templates/'
		);
	}

	public function save_product() {
		check_ajax_referer( 'webspark_product_upload', 'security' );

		$product_id = isset( $_POST['product_id'] ) ? intval( $_POST['product_id'] ) : 0;

		// Validate and sanitize input
		$product_data = array(
			'name'        => sanitize_text_field( $_POST['product_name'] ),
			'price'       => floatval( $_POST['product_price'] ),
			'stock'       => intval( $_POST['product_stock'] ),
			'description' => wp_kses_post( $_POST['product_description'] ),
			'image_id'    => intval( $_POST['product_image'] ),
		);

		// Create or update product
		$product = $product_id ? wc_get_product( $product_id ) : new WC_Product();
		$product->set_name( $product_data['name'] );
		$product->set_price( $product_data['price'] );
		$product->set_manage_stock( true );
		$product->set_stock_quantity( $product_data['stock'] );
		$product->set_description( $product_data['description'] );
		$product->set_status( 'pending' );

		// Set product image
		if ( $product_data['image_id'] ) {
			$product->set_image_id( $product_data['image_id'] );
		}

		$product_id = $product->save();

		// Trigger email notification
		do_action( 'webspark_product_created_or_updated', $product_id );

		wp_send_json_success(
			array(
				'message'    => __( 'Product saved successfully', 'wp-my-product-webspark' ),
				'product_id' => $product_id,
			)
		);
	}

	public function edit_product() {
		check_ajax_referer( 'webspark_product_edit', 'security' );

		$product_id = intval( $_POST['product_id'] );
		$product    = wc_get_product( $product_id );
		$author_id  = (int) get_post_field( 'post_author', $product->get_id() );

		if ( $product_id && $author_id === get_current_user_id() ) {
			// Validate and sanitize input
			$product_data = array(
				'name'        => sanitize_text_field( $_POST['product_name'] ),
				'price'       => floatval( $_POST['product_price'] ),
				'stock'       => intval( $_POST['product_stock'] ),
				'description' => wp_kses_post( $_POST['product_description'] ),
				'image_id'    => intval( $_POST['product_image'] ),
			);

			// Update product
			$product->set_name( $product_data['name'] );
			$product->set_price( $product_data['price'] );
			$product->set_manage_stock( true );
			$product->set_stock_quantity( $product_data['stock'] );
			$product->set_description( $product_data['description'] );

			// Set product image
			if ( $product_data['image_id'] ) {
				$product->set_image_id( $product_data['image_id'] );
			}

			$product->save();

			wp_send_json_success(
				array(
					'message'    => __( 'Product edited successfully', 'wp-my-product-webspark' ),
					'product_id' => $product_id,
				)
			);
		}

		wp_send_json_error( __( 'Unable to edit product', 'wp-my-product-webspark' ) );
	}

	public function delete_product() {
		check_ajax_referer( 'webspark_product_delete', 'security' );

		$product_id = intval( $_POST['product_id'] );
		$product    = wc_get_product( $product_id );
		$author_id  = (int) get_post_field( 'post_author', $product->get_id() );

		if ( $product_id && $author_id === get_current_user_id() ) {
			wp_delete_post( $product_id, true );
			wp_send_json_success( __( 'Product deleted successfully', 'wp-my-product-webspark' ) );
		}

		wp_send_json_error( __( 'Unable to delete product', 'wp-my-product-webspark' ) );
	}
}

MyProductHandler::get_instance();
