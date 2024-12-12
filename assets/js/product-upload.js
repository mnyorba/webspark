jQuery(document).ready(function($) {
    // Image upload handler
    $('#upload_image_button').on('click', function(e) {
        e.preventDefault();
        
        var image_frame;
        if (image_frame) {
            image_frame.open();
        }
        
        // Define image_frame as wp.media object
        image_frame = wp.media({
            title: 'Select Product Image',
            multiple: false,
            library: {
                type: 'image'
            }
        });
        
        image_frame.on('select', function() {
            var attachment = image_frame.state().get('selection').first().toJSON();
            
            $('#product_image_id').val(attachment.id);
            $('#product_image_preview')
                .attr('src', attachment.url)
                .show();
        });
        
        image_frame.open();
    });

    // Form submission handler
    $('#webspark-product-form').on('submit', function(e) {
        e.preventDefault();
        
        var form_data = new FormData(this);
        form_data.append('action', 'webspark_save_product');
        form_data.append('security', webspark_product_params.upload_nonce);
        
        $.ajax({
            url: webspark_product_params.ajax_url,
            type: 'POST',
            data: form_data,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    window.location.href = webspark_product_params.my_products_url;
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('An error occurred while saving the product.');
            }
        });
    });
});