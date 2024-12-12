jQuery(document).ready(function ($) {
// Product edit handler
$('.edit-product').on('click', function (e) {
    e.preventDefault();
    
    var productId = $(this).data('product-id');
    window.location.href = webspark_product_params.edit_product_url + '?edit=' + productId;
});

 // Edit product form handler
$('#edit-product-form').on('submit', function (e) {
    e.preventDefault();
    
    var productId = $(this).data('product-id');
    
    // Відправити AJAX-запит для отримання даних про продукт
    $.ajax({
        type: 'POST',
        url: webspark_product_params.ajax_url,
        data: {
            action: 'webspark_product_edit',
            product_id: productId,
            security: webspark_product_params.edit_nonce
        },
        success: function (response) {
            if (response.success) {
                // Заповнити поля форми даними про продукт
                $('#product-name').val(response.data.product_name);
                $('#product-price').val(response.data.product_price);
                $('#product-stock').val(response.data.product_stock);
                $('#product-description').val(response.data.product_description);
                $('#product-image-id').val(response.data.product_image_id);
            } else {
                alert(response.data.message);
            }
        }
    });
});
	
	
});