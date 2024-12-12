jQuery(document).ready(function ($) {
    // Product deletion handler
    $('.delete-product').on('click', function (e) {
        e.preventDefault();

        if (!confirm('Are you sure you want to delete this product?')) {
            return;
        }

        var product_id = $(this).data('product-id');

        $.ajax({
            url: webspark_product_params.ajax_url,
            type: 'POST',
            data: {
                action: 'webspark_delete_product',
                product_id: product_id,
                security: webspark_product_params.delete_nonce
            },
            success: function (response) {
                if (response.success) {
                    alert(response.data);
                    location.reload();
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function () {
                alert('An error occurred while deleting the product.');
            }
        });
    });
});