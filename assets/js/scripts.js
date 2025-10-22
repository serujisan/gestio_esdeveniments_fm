jQuery(document).ready(function($) {
    
    // Filtros de esdeveniments
    $('#ge-apply-filters').on('click', function() {
        var provincia = $('#filter_provincia').val();
        var poblacio = $('#filter_poblacio').val();
        var categoria = $('#filter_categoria').val();
        
        $.ajax({
            url: geAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'ge_filter_events',
                provincia: provincia,
                poblacio: poblacio,
                categoria: categoria
            },
            beforeSend: function() {
                $('#ge-events-list').html('<p style="text-align:center;">Carregant...</p>');
            },
            success: function(response) {
                $('#ge-events-list').html(response);
            },
            error: function() {
                $('#ge-events-list').html('<p style="text-align:center;color:red;">Error al carregar els esdeveniments.</p>');
            }
        });
    });
    
    // Reset filters
    $('#ge-reset-filters').on('click', function() {
        $('#filter_provincia').val('');
        $('#filter_poblacio').val('');
        $('#filter_categoria').val('');
        $('#ge-apply-filters').trigger('click');
    });
    
    // Submit formulari esdeveniment
    $('#ge-esdeveniment-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        formData.append('action', 'ge_submit_event');
        
        $.ajax({
            url: geAjax.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#ge-form-message').removeClass('success error').html('Enviant...').show();
            },
            success: function(response) {
                if (response.success) {
                    $('#ge-form-message').addClass('success').html(response.data);
                    $('#ge-esdeveniment-form')[0].reset();
                } else {
                    $('#ge-form-message').addClass('error').html(response.data);
                }
            },
            error: function() {
                $('#ge-form-message').addClass('error').html('Error al enviar el formulari.');
            }
        });
    });
});
