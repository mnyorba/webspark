<?php
/**
 * New Product Notification Email Template (HTML)
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $email->get_subject(); ?></title>
</head>
<body>
	<div class="email-container" style="max-width: 600px; margin: 0 auto; padding: 20px;">
		<h1><?php _e( 'New Product Submitted', 'wp-my-product-webspark' ); ?></h1>
		
		<p><?php printf( __( 'A new product has been submitted by %s.', 'wp-my-product-webspark' ), $object['author_name'] ); ?></p>
		
		<table style="width: 100%; border-collapse: collapse;">
			<tr>
				<th style="text-align: left; border: 1px solid #ddd; padding: 8px;">
					<?php _e( 'Product Details', 'wp-my-product-webspark' ); ?>
				</th>
			</tr>
			<tr>
				<td style="border: 1px solid #ddd; padding: 8px;">
					<strong><?php _e( 'Product Name:', 'wp-my-product-webspark' ); ?></strong> 
					<?php echo esc_html( $object['product_name'] ); ?>
				</td>
			</tr>
			<tr>
				<td style="border: 1px solid #ddd; padding: 8px;">
					<strong><?php _e( 'Author:', 'wp-my-product-webspark' ); ?></strong> 
					<?php echo esc_html( $object['author_name'] ); ?>
				</td>
			</tr>
		</table>
		
		<p>
			<a href="<?php echo esc_url( $object['edit_product_link'] ); ?>">
				<?php _e( 'Review Product', 'wp-my-product-webspark' ); ?>
			</a>
		</p>
		
		<p><?php _e( 'Please review and take necessary actions.', 'wp-my-product-webspark' ); ?></p>
	</div>
</body>
</html>