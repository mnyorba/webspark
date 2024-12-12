<?php
/**
 * My Products Template with Detailed Product Rendering
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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

wp_enqueue_script( 'webspark-product-management', WEBSPARK_MY_PRODUCT_URL . 'assets/js/product-management.js', array( 'jquery' ), '1.0', true );
wp_localize_script(
	'webspark-product-management',
	'webspark_product_params',
	array(
		'ajax_url'        => admin_url( 'admin-ajax.php' ),
		'delete_nonce'    => wp_create_nonce( 'webspark_product_delete' ),
		'add_product_url' => wc_get_endpoint_url( 'add-product' ),
	)
);
?>

<div class="woocommerce-my-products">
	<h2><?php _e( 'My Products', 'wp-my-product-webspark' ); ?></h2>

	<?php if ( $products_query->have_posts() ) : ?>
		<table class="shop_table shop_table_responsive">
			<thead>
				<tr>
					<th><?php _e( 'Thumbnail', 'wp-my-product-webspark' ); ?></th>
					<th><?php _e( 'Product Name', 'wp-my-product-webspark' ); ?></th>
					<th><?php _e( 'Quantity', 'wp-my-product-webspark' ); ?></th>
					<th><?php _e( 'Price', 'wp-my-product-webspark' ); ?></th>
					<th><?php _e( 'Status', 'wp-my-product-webspark' ); ?></th>
					<th><?php _e( 'Actions', 'wp-my-product-webspark' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				while ( $products_query->have_posts() ) :
					$products_query->the_post();
					?>
					<?php
					$product   = wc_get_product( get_the_ID() );
					$thumbnail = $product->get_image( 'thumbnail' ) ?: '<img src="' . wc_placeholder_img_src( 'thumbnail' ) . '" alt="Placeholder" />';
					?>
					<tr>
						<td class="product-thumbnail" data-title="<?php _e( 'Thumbnail', 'wp-my-product-webspark' ); ?>">
							<?php echo $thumbnail; ?>
						</td>
						<td class="product-name" data-title="<?php _e( 'Name', 'wp-my-product-webspark' ); ?>">
							<?php echo esc_html( $product->get_name() ); ?>
						</td>
						<td class="product-quantity" data-title="<?php _e( 'Quantity', 'wp-my-product-webspark' ); ?>">
							<?php echo $product->get_stock_quantity() ?: __( 'N/A', 'wp-my-product-webspark' ); ?>
						</td>
						<td class="product-price" data-title="<?php _e( 'Price', 'wp-my-product-webspark' ); ?>">
							<?php echo wc_price( $product->get_price() ); ?>
						</td>
						<td class="product-status" data-title="<?php _e( 'Status', 'wp-my-product-webspark' ); ?>">
							<?php
							$status_mapping = array(
								'pending' => __( 'Pending Review', 'wp-my-product-webspark' ),
								'publish' => __( 'Published', 'wp-my-product-webspark' ),
								'draft'   => __( 'Draft', 'wp-my-product-webspark' ),
								'private' => __( 'Private', 'wp-my-product-webspark' ),
							);
							echo $status_mapping[ $product->get_status() ] ?? $product->get_status();
							?>
						</td>
						<td class="product-actions" data-title="<?php _e( 'Actions', 'wp-my-product-webspark' ); ?>">
							<div class="actions">
								<a href="#" 
									class="edit-product button" 
									data-product-id="<?php echo $product->get_id(); ?>">
									<?php _e( 'Edit', 'wp-my-product-webspark' ); ?>
								</a>
								<a href="#" 
									class="delete-product button" 
									data-product-id="<?php echo $product->get_id(); ?>">
									<?php _e( 'Delete', 'wp-my-product-webspark' ); ?>
								</a>
							</div>
						</td>
					</tr>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</tbody>
		</table>

		<div class="woocommerce-pagination">
			<?php
			$total_pages  = $products_query->max_num_pages;
			$current_page = max( 1, $paged );

			echo paginate_links(
				array(
					'base'      => wc_get_endpoint_url( 'my-products' ) . '%_%',
					'format'    => 'page/%#%',
					'current'   => $current_page,
					'total'     => $total_pages,
					'prev_text' => '&laquo;',
					'next_text' => '&raquo;',
				)
			);
			?>
		</div>

	<?php else : ?>
		<div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
			<?php _e( 'No products found.', 'wp-my-product-webspark' ); ?>
			<a href="<?php echo wc_get_endpoint_url( 'add-product' ); ?>" class="button">
				<?php _e( 'Add Your First Product', 'wp-my-product-webspark' ); ?>
			</a>
		</div>
	<?php endif; ?>
</div>