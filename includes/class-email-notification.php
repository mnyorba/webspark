<?php
namespace WebSpark\MyProduct;

use WC_Email;

class EmailNotification extends WC_Email {
	use SingletonTrait;

	public function __construct() {
		$this->id          = 'webspark_product_notification';
		$this->title       = __( 'New Product Notification', 'wp-my-product-webspark' );
		$this->description = __( 'Notification sent to admin when a new product is created or updated', 'wp-my-product-webspark' );

		$this->template_base  = WEBSPARK_MY_PRODUCT_PATH . 'templates/emails/';
		$this->template_html  = 'new-product-notification.php';
		$this->template_plain = 'plain/new-product-notification.php';

		add_action( 'webspark_product_created_or_updated', array( $this, 'trigger' ), 10, 1 );

		parent::__construct();
	}

	public function trigger( $product_id ) {
		if ( ! $this->is_enabled() ) {
			return;
		}

		$product   = wc_get_product( $product_id );
		$author_id = (int) get_post_field( 'post_author', $product->get_id() );
		$author    = get_userdata( $author_id );

		$this->recipient = get_option( 'admin_email' );
		$this->object    = array(
			'product_id'        => $product_id,
			'product_name'      => $product->get_name(),
			'author_id'         => $author->ID,
			'author_name'       => $author->display_name,
			'edit_product_link' => admin_url( 'post.php?post=' . $product_id . '&action=edit' ),
		);

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), array() );
	}

	public function get_subject() {
		return __( 'New Product Submitted: {product_name}', 'wp-my-product-webspark' );
	}

	public function get_heading() {
		return __( 'New Product Submitted', 'wp-my-product-webspark' );
	}

	public function get_content() {
		return $this->get_content_html();
	}

	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'   => __( 'Enable/Disable', 'wp-my-product-webspark' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable notification email', 'wp-my-product-webspark' ),
				'default' => 'yes',
			),
		);
	}
}
