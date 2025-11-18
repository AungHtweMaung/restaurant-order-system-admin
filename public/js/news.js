// Initialize pairIndex from existing content-pairs in DOM
let pairIndex = $('#content-container .content-pair').length;

$('#add-more-contents').on('click', function () {
    // Generate new content pair with updated pairIndex
    let newPair = `
        <div class="content-pair mb-3 border-top pt-3" data-index="${pairIndex}">
            <div class="form-group">
                <div class="d-flex justify-content-between align-items-end mb-2">
                    <label>Content</label>
                    <button type="button" class="btn btn-danger text-white remove-pair">
                        <i class="fa-regular fa-circle-xmark"></i>
                    </button>
                </div>
                <textarea class="form-control content-summernote" name="news[${pairIndex}][content]" rows="4"></textarea>
                <div class="invalid-feedback" data-error-for="news[${pairIndex}][content]"></div>
            </div>

            <div class="form-group">
                <label>Image</label>
                <input type="file" class="form-control" name="news[${pairIndex}][image]" required>
                <div class="invalid-feedback" data-error-for="news[${pairIndex}][image]"></div>
            </div>
        </div>
    `;
    // Append new pair to the container
    $('#content-container').append(newPair);

    // Initialize Summernote for the newly added textarea
    $('#content-container .content-summernote').last().summernote({
        height: 300,   // Set editor height
        placeholder: 'Enter content here...',
        fontSizes: ['8', '9', '10', '11', '12', '14', '16', '18', '20', '22', '24', '28', '32', '36', '48', '64'],
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol']],
            ['insert', ['link']],
            ['view', ['codeview']]
        ]
    });

    // Increment pairIndex for the next added pair
    pairIndex++;
});


// Handle removal of content pair
$(document).on('click', '.remove-pair', function () {
    let pair = $(this).closest('.content-pair');

    // Ensure the ID input field is removed

    // Remove the content pair element
    pair.remove();

    // Reindex the remaining content pairs
    reindexPairs();
});

function reindexPairs() {
    let pairIndex = 0;

    $('#content-container .content-pair').each(function () {
        $(this).attr('data-index', pairIndex);

        // Update name for content
        $(this).find('textarea.content-summernote')
            .attr('name', `news[${pairIndex}][content]`);

        // Update name for image
        $(this).find('input[type="file"]')
            .attr('name', `news[${pairIndex}][image]`)
            .attr('id', `image_${pairIndex}`);

        // Update error feedbacks
        $(this).find('.invalid-feedback[data-error-for]').each(function () {
            const errorFor = $(this).attr('data-error-for');
            if (errorFor.includes('[content]')) {
                $(this).attr('data-error-for', `news[${pairIndex}][content]`);
            } else if (errorFor.includes('[image]')) {
                $(this).attr('data-error-for', `news[${pairIndex}][image]`);
            }
        });

        // Update ID field name if exists
        const idField = $(this).find('input[name^="news["][name$="[id]"]');
        if (idField.length) {
            idField.attr('name', `news[${pairIndex}][id]`);
        }

        pairIndex++;
    });
}



// store, update form submit
    $('.form-submit-multiple-contents').submit(function (e) {
        e.preventDefault();

        // Loop through and clean up all removed content pairs
        $('#content-container .content-pair').each(function () {
            let index = $(this).data('index');
            let idField = $(this).find(`input[name="news[${index}][id]"]`);

            // If the ID field is empty (because it was removed), remove it from the form data
            if (!idField.length || idField.val() === '') {
                idField.remove();  // Remove the ID field completely
            }
        });



        $('.content-summernote').each(function() {
            var editorContent = $(this).summernote('code');
            const cleanContent = editorContent.replace(/<script.*?>.*?<\/script>/gi, '');

            const isOnlyHtmlTags = cleanContent.replace(/<[^>]+>/g, '').trim() === '';

            if (isOnlyHtmlTags) {
                $(this).summernote('code', '');
            } else {
                $(this).summernote('code', cleanContent);
            }
        });



        reindexPairs(); // Ensure pairs are reindexed before submission

        let form = $(this);
        let actionUrl = form.attr('action');
        let formData = new FormData(this);  // send as Form Data
        // Remove previous error states
        form.find('.invalid-feedback').html('');
        form.find('input, select, textarea').removeClass('is-invalid');


        $.ajax({
            url: actionUrl,
            type: 'POST',
            data: formData,
            // Prevent jQuery from automatically converting data into a query string.
            processData: false, // don't process data by jquery and send as FormData
            contentType: false, // choose correct content type by browser
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Laravel CSRF support
            },
            beforeSend: function () {
                $('.loader-container').show();
            },
            complete: function () {
                $('.loader-container').hide();
            },
            success: function (response) {
                $('#content').summernote('reset'); // reset to initial content
                form[0].reset();

                Swal.fire({
                    title: 'Success',
                    text: response.success,
                    icon: 'success',
                    confirmButtonText: 'OK',
                    allowOutsideClick: false, // optional: prevent closing by clicking outside
                    allowEscapeKey: false
                }).then((result) => {
                    // console.log(result);
                    if (result.isConfirmed) {
                        // Reset Summernote content if present in the form
                        if (response.redirectUrl) {
                            window.location.href = response.redirectUrl;
                        }
                    }
                });
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    // console.log(errors);
                    // $.each(errors, function (field, messages) {
                    //     let errorField = form.find('[name="' + field + '"]');
                    //     errorField.addClass('is-invalid');
                    //     form.find('[data-error-for="' + field + '"]').html(messages[0]);
                    // });

                    $.each(errors, function (field, messages) {
                        // error is like news.0.content and news.0.image
                        // We need to convert it to news[0][content] and news[0][image]

                        let errorField = field.replace(/\.(\d+)\./g, '[$1][')   // news.0.content => news[0][content]
                            .replace(/\.(\w+)/g, '][$1]');    // append last key
                        errorField = field.includes('.') ? errorField + ']' : errorField;  // close the brackets if needed

                        // Escape the name for jQuery selector (to use with attributes like name="news[0][content]")
                        let escapedField = errorField.replace(/\[/g, '\\[').replace(/\]/g, '\\]');

                        let input = form.find('[name="' + errorField + '"]');
                        input.addClass('is-invalid');

                        // You should have something like: <div data-error-for="news[0][content]"></div>
                        form.find('[data-error-for="' + escapedField + '"]').html(messages[0]);
                    });

                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'Oops! Something went wrong.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            }
        });

    });




