jQuery(function($) {
    $('#pori_alonaati').change(function() {
        if (!this.files.length) {
            $('#pori_list_upload').empty();
        } else {
            const file = this.files[0];
            const fileType = file.type;

           
            if (fileType.startsWith('image/') || fileType === 'application/pdf') {
                $('#pori_list_upload').html('<img src="' + URL.createObjectURL(file) + '"><span>' + file.name + '</span>');
                
                const formData = new FormData();
                formData.append('pori_alonaati', file); 

                $.ajax({
                    url: wc_checkout_params.ajax_url + '?action=porialonaatiupload', 
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    enctype: 'multipart/form-data',
                    processData: false,
                    success: function(response) {
                        
                        $('input[name="pori_alonaati_field"]').val(response);
                    },
                    error: function(response) {
                        alert('Error uploading file');
                    }
                });
            } else {
                alert('فقط فیش با پسوند png , jpg , pdf مورد قبول می باشد.');
                $('#pori_list_upload').empty();
            }
        }
    });
	
});

