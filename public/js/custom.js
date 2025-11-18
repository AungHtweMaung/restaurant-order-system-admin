


$(document).ready(function () {
    // for select2
    $('select').select2();

    // flatpickr date time
    $('.month-year-picker').flatpickr({
        // dateFormat: "Y-m",     // Format: 2025-06
        plugins: [
            new monthSelectPlugin({
                shorthand: true, //defaults to false
                dateFormat: "M Y", //defaults to "F Y"
                altFormat: "F Y", //defaults to "F Y"
                // theme: "dark" // defaults to "light"
            })
        ]
    });

    $(".date-picker").flatpickr({
        allowInput: true,
    });

    $('.time-picker').flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "h:i K",
        allowInput: true,
    });





    // store, update form submit
    $('.form-submit').submit(function (e) {
        e.preventDefault();

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
                    console.log(result);
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

    // for comment notifications
    $('.noti-icon').on('click', function (e) {
        e.preventDefault();

        loadNotifications(); // you can wrap the above AJAX in a function called loadNotifications()
    });

    // Optional: mark notification as read when clicked
    $(document).on('click', '.preview-item', function () {
        let notificationId = $(this).data('id');

        $.ajax({
            url: '/notifications/mark-as-read', // or window.Laravel.markAsReadUrl
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: { id: notificationId },
            success: function () {
                // Optionally remove the notification from the list or refresh
                loadNotifications(); // you can wrap the above AJAX in a function called loadNotifications()
            }
        });
    });


    function loadNotifications() {
        $.ajax({
            url: '/notifications/unread', // You can also use window.Laravel.unreadNotificationsUrl if passed via @json
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Laravel CSRF support
            },
            success: function (response) {
                let notifications = response.latestNotiCount;

                let html = '';

                $allNotiCount = response.allNotiCount;
                if ($allNotiCount > 0) {
                    $('#notification-count').text($allNotiCount).show();
                } else {
                    $('#notification-count').hide();
                }

                if (notifications.length === 0) {
                    html = '<p class="dropdown-item">No new notifications</p>';
                } else {
                    notifications.forEach(function (noti) {
                        html += `
                        <a href="${noti.data.url || '#'}" class="dropdown-item preview-item" data-id="${noti.id}">
                            <div class="preview-item-content">
                                <p class="preview-subject mb-1">${noti.data.message}</p>
                                <p class="text-muted ellipsis mb-0">${new Date(noti.created_at).toLocaleString()}</p>
                            </div>
                        </a>
                    `;
                    });
                }

                $('#noti_list').html(html);
            },
            error: function (xhr) {
                console.log('Error fetching notifications', xhr);
            }
        });
    }





    $('.chat-noti-icon').on('click', function (e) {
        e.preventDefault();

        chatLoadNotifications(); // you can wrap the above AJAX in a function called loadNotifications()
    });

    // Optional: mark notification as read when clicked
    // $(document).on('click', '.preview-item', function () {
    //     let notificationId = $(this).data('id');

    //     $.ajax({
    //         url: '/notifications/mark-as-read', // or window.Laravel.markAsReadUrl
    //         type: 'POST',
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         data: { id: notificationId },
    //         success: function () {
    //             // Optionally remove the notification from the list or refresh
    //             chatLoadNotifications(); // you can wrap the above AJAX in a function called loadNotifications()
    //         }
    //     });
    // });


    function chatLoadNotifications() {
        $.ajax({
            url: '/notifications/unread/chat',
            type: 'GET',

            success: function (response) {
                let notifications = response.latestChatNotifications;
                console.log(notifications);

                let html = '';

                $allNotiCount = response.allChatNotiCount;
                if ($allNotiCount > 0) {
                    $('#chat-notification-count').text($allNotiCount).show();
                } else {
                    $('#chat-notification-count').hide();
                }

                if (notifications.length === 0) {
                    html = '<p class="dropdown-item">No new notifications</p>';
                } else {
                    notifications.forEach(function (noti) {
                        html += `
                        <a href="${noti.data.url || '#'}" class="dropdown-item preview-item chat-notification" data-id="${noti.id}" data-sender-id="${noti.data.sender_id}">
                            <div class="preview-item-content">
                                <p class="preview-subject mb-1">${noti.data.message}</p>
                                <p class="text-muted ellipsis mb-0">${new Date(noti.created_at).toLocaleString()}</p>
                            </div>
                        </a>
                    `;
                    });
                }

                $('#chat_noti_list').html(html);
            },
            error: function (xhr) {
                console.log('Error fetching notifications', xhr);
            }
        });
    }

    // Handle click on chat notification to redirect and mark as read
    $(document).on('click', '.chat-notification', function(e) {
        e.preventDefault();
        let notificationId = $(this).data('id');
        let senderId = $(this).data('sender-id');

        // Store senderId in localStorage
        localStorage.setItem('chat_sender_id', senderId);

        // Mark notification as read
        $.ajax({
            url: '/notifications/mark-as-read',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: { id: notificationId },
            success: function() {
                // Redirect to chat page
                window.location.href = '/chats';
            },
            error: function() {
                // Redirect anyway if error
                window.location.href = '/chats';
            }
        });
    });




    // // store, update form submit
    // $('.form-submit').submit(function (e) {
    //     e.preventDefault();

    //     // Loop through and clean up all removed content pairs
    //     $('#content-container .content-pair').each(function () {
    //         let index = $(this).data('index');
    //         let idField = $(this).find(`input[name="news[${index}][id]"]`);

    //         // If the ID field is empty (because it was removed), remove it from the form data
    //         if (!idField.length || idField.val() === '') {
    //             idField.remove();  // Remove the ID field completely
    //         }
    //     });



    //     $('.content-summernote').each(function() {
    //         var editorContent = $(this).summernote('code');
    //         const cleanContent = editorContent.replace(/<script.*?>.*?<\/script>/gi, '');

    //         const isOnlyHtmlTags = cleanContent.replace(/<[^>]+>/g, '').trim() === '';

    //         if (isOnlyHtmlTags) {
    //             $(this).summernote('code', '');
    //         } else {
    //             $(this).summernote('code', cleanContent);
    //         }
    //     });



    //     reindexPairs(); // Ensure pairs are reindexed before submission

    //     let form = $(this);
    //     let actionUrl = form.attr('action');
    //     let formData = new FormData(this);  // send as Form Data
    //     // Remove previous error states
    //     form.find('.invalid-feedback').html('');
    //     form.find('input, select, textarea').removeClass('is-invalid');


    //     $.ajax({
    //         url: actionUrl,
    //         type: 'POST',
    //         data: formData,
    //         // Prevent jQuery from automatically converting data into a query string.
    //         processData: false, // don't process data by jquery and send as FormData
    //         contentType: false, // choose correct content type by browser
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Laravel CSRF support
    //         },
    //         beforeSend: function () {
    //             $('.loader-container').show();
    //         },
    //         complete: function () {
    //             $('.loader-container').hide();
    //         },
    //         success: function (response) {
    //             $('#content').summernote('reset'); // reset to initial content
    //             form[0].reset();

    //             Swal.fire({
    //                 title: 'Success',
    //                 text: response.success,
    //                 icon: 'success',
    //                 confirmButtonText: 'OK',
    //                 allowOutsideClick: false, // optional: prevent closing by clicking outside
    //                 allowEscapeKey: false
    //             }).then((result) => {
    //                 // console.log(result);
    //                 if (result.isConfirmed) {
    //                     // Reset Summernote content if present in the form
    //                     if (response.redirectUrl) {
    //                         window.location.href = response.redirectUrl;
    //                     }
    //                 }
    //             });
    //         },
    //         error: function (xhr) {
    //             if (xhr.status === 422) {
    //                 let errors = xhr.responseJSON.errors;
    //                 // console.log(errors);
    //                 // $.each(errors, function (field, messages) {
    //                 //     let errorField = form.find('[name="' + field + '"]');
    //                 //     errorField.addClass('is-invalid');
    //                 //     form.find('[data-error-for="' + field + '"]').html(messages[0]);
    //                 // });

    //                 $.each(errors, function (field, messages) {
    //                     // error is like news.0.content and news.0.image
    //                     // We need to convert it to news[0][content] and news[0][image]

    //                     let errorField = field.replace(/\.(\d+)\./g, '[$1][')   // news.0.content => news[0][content]
    //                         .replace(/\.(\w+)/g, '][$1]');    // append last key
    //                     errorField = field.includes('.') ? errorField + ']' : errorField;  // close the brackets if needed

    //                     // Escape the name for jQuery selector (to use with attributes like name="news[0][content]")
    //                     let escapedField = errorField.replace(/\[/g, '\\[').replace(/\]/g, '\\]');

    //                     let input = form.find('[name="' + errorField + '"]');
    //                     input.addClass('is-invalid');

    //                     // You should have something like: <div data-error-for="news[0][content]"></div>
    //                     form.find('[data-error-for="' + escapedField + '"]').html(messages[0]);
    //                 });

    //             } else {
    //                 Swal.fire({
    //                     title: 'Error',
    //                     text: 'Oops! Something went wrong.',
    //                     icon: 'error',
    //                     confirmButtonText: 'OK'
    //                 });
    //             }
    //         }
    //     });

    // });



    $('.delete-data').on('click', function (e) {
        e.preventDefault();
        Swal.fire({
            title: "Are you sure to delete?",
            confirmButtonColor: '#3085d6',
            icon: 'info',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes',
            showCancelButton: true,
            reverseButtons: true,

        }).then((result) => {
            if (result.isConfirmed) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    method: 'DELETE',
                    url: $(this).attr('data-href'),
                    beforeSend: function () {
                        $('.loader-container').show();
                    },
                    complete: function () {
                        $('.loader-container').hide();
                    },
                    success: function (response) {

                        if (response.success) {
                            Swal.fire({
                                title: 'Success',
                                text: response.success,
                                icon: 'success',
                                confirmButtonText: 'OK',
                                allowOutsideClick: false,
                                allowEscapeKey: false
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.reload();
                                }
                            });
                        }
                        if (response.error) {
                            Swal.fire({
                                title: 'Error',
                                text: response.error,
                                icon: 'error',
                                confirmButtonText: 'OK',
                                allowOutsideClick: false,
                                allowEscapeKey: false
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.reload();
                                }
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        Swal.fire({
                            title: 'Error',
                            text: 'Something went wrong!',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    });

    $('.content-summernote').summernote({
        dialogsInBody: true,
        disableDragAndDrop: true,
        height: 300,
        placeholder: 'Enter content here...',
        fontSizes: ['8', '9', '10', '11', '12', '14', '16', '18', '20', '22', '24', '28', '32', '36', '48', '64'],
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol']],
            ['insert', ['link']], // Added 'unlink' for better UX
            ['view', ['codeview']]
        ],
        // Add these additional settings
        popover: {
            link: [
                ['link', ['linkDialogShow', 'unlink']]
            ]
        },
        // callbacks: {
        //     onInit: function () {
        //         console.log('Summernote initialized');
        //     }
        // }
    });
});
