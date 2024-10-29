jQuery(document).ready(function($) {
    $('#custom-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', 'process_custom_form');
        formData.append('nonce', customForm.nonce);
        
        // Limpiar mensajes de error previos
        $('.error-message').text('');
        
        $.ajax({
            url: customForm.ajaxUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert('Formulario enviado correctamente');
                    $('#custom-form')[0].reset();
                } else {
                    // Mostrar errores
                    Object.keys(response.data).forEach(function(key) {
                        $(`[data-error="${key}"]`).text(response.data[key]);
                    });
                }
            },
            error: function() {
                alert('Error al procesar el formulario');
            }
        });
    });
});