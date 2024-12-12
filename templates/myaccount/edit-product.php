<?php
/**
 * Edit Product Template
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$product_id = $_GET['product_id'];
$product = wc_get_product($product_id);

wp_enqueue_media();
wp_enqueue_script( 'webspark-product-edit', WEBSPARK_MY_PRODUCT_URL . 'assets/js/product-edit.js', array( 'jquery' ), '1.0', true );
wp_localize_script(
	'webspark-product-edit',
	'webspark_product_params',
	array(
		'ajax_url'         => admin_url( 'admin-ajax.php' ),
		'edit_nonce'       => wp_create_nonce( 'webspark_product_edit' ),
		'edit_product_url' => wc_get_endpoint_url( 'edit-product' ),
	)
);
?>

<div class="woocommerce-edit-product">
	<form id="webspark-product-form" method="post" enctype="multipart/form-data">
		<div class="form-group">
			<label for="product_name"><?php _e( 'Product Name', 'wp-my-product-webspark' ); ?></label>
			<input type="text" name="product_name" id="product_name" value="<?php echo $product->get_name(); ?>" required>
		</div>

		<div class="form-group">
			<label for="product_price"><?php _e( 'Product Price', 'wp-my-product-webspark' ); ?></label>
			<input type="number" name="product_price" id="product_price" step="0.01" value="<?php echo $product->get_price(); ?>" required>
		</div>

		<div class="form-group">
			<label for="product_stock"><?php _e( 'Stock Quantity', 'wp-my-product-webspark' ); ?></label>
			<input type="number" name="product_stock" id="product_stock" value="<?php echo $product->get_stock_quantity(); ?>" required>
		</div>

		<div class="form-group">
			<label for="product_description"><?php _e( 'Product Description', 'wp-my-product-webspark' ); ?></label>
			<?php
			wp_editor(
				$product->get_description(),
				'product_description',
				array(
					'media_buttons' => true,
					'teeny'         => false,
					'tinymce'       => true,
				)
			);
			?>
		</div>

		<div class="form-group">
			<label for="product_image"><?php _e( 'Product Image', 'wp-my-product-webspark' ); ?></label>
			<input type="hidden" name="product_image" id="product_image_id" value="<?php echo $product->get_image_id(); ?>">
			<img id="product_image_preview" src="<?php echo wp_get_attachment_url( $product->get_image_id() ); ?>" style="max-width: 300px;">
			<button type="button" id="upload_image_button" class="button">
				<?php _e( 'Upload Image', 'wp-my-product-webspark' ); ?>
			</button>
		</div>

		<div class="form-group">
			<input type="submit" value="<?php _e( 'Save Changes Edit', 'wp-my-product-webspark' ); ?>" class="button">
		</div>
	</form>
</div>