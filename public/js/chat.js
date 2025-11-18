
        // Initialize Pusher
        // var loggedUserInfo = @json($LoggedUserInfo);
        // var receiverId = loggedUserInfo.id; // The receiverId will be the logged user's ID
        var pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            encrypted: true
        });


        // Subscribe to the public channel based on the receiverId
        var channel = pusher.subscribe('my-channel.' + receiverId); // Use dynamic receiverId

        // Bind to the 'message' event
        channel.bind('my-event', function(data) {
            // console.log('Message received:', data);

            let senderId = data.sender_id;
            let message = data.message;
            let senderName = data.sender_name;
            let senderImage = data.sender_image ?
                `{{ asset('storage/') }}/${data.sender_image}` // Construct the full asset URL
                :
                `{{ asset('src/assets/images/avatar-default (1).svg') }}`; // Default image

            let messageTime = data.time || new Date().toLocaleTimeString([], { // Use `time` from the payload
                hour: '2-digit',
                minute: '2-digit'
            });

            // Check if the logged-in user is the receiver before displaying the message
            if (data.receiver_id == receiverId) {
                let messageHtml = `
            <div class="chat-message receiver">
                <div class="message-avatar">
                    <img src="${senderImage}" class="rounded-circle avatar" alt="${senderName} Avatar">
                </div>
                <div class="message-content">
                    <p><strong>${senderName}:</strong> ${message}</p>
                    <div class="timestamp">${messageTime}</div>
                </div>
            </div>`;

                // Append message to chat container
                document.getElementById('chatMessageContainer').insertAdjacentHTML('beforeend', messageHtml);

                // document.getElementById('chatMessageContainer').append(messageHtml);

                // Scroll to the bottom of the chat container
                $('#chatMessageContainer').scrollTop($('#chatMessageContainer')[0].scrollHeight);
            }
        });

        $('.chat-item').on('click', function() {
            $('.chat-area').removeClass('d-none');
            let profileImage = $(this).find('.profile_img').attr('src');
            let profileName = $(this).find('.profile_name').text();
            let receiverId = $(this).find('.id').text();
            $('#receiver_id').val(receiverId);
            $('#chat_img').attr('src', profileImage);
            $('#chat_name').text(profileName);

            // $.ajax({
            //     url: '{{ route('fetch.messages') }}',
            //     method: 'GET',
            //     data: {
            //         receiver_id: receiverId
            //     },
            //     success: function(response) {
            //         console.log('Messages fetched:', response.messages);
            //         $('#chatMessageContainer').empty();

            //         response.messages.forEach(function(message) {
            //             let isSender = message.sender_id == '{{ session('LoggedUserInfo') }}';
            //             let userAvatar = isSender ?
            //                 '{{  $LoggedUserInfo->picture ? asset("storage/" . $LoggedUserInfo->picture) : asset("src/assets/images/avatar-default (1).svg") }}' : profileImage;
            //             console.log(userAvatar);
            //             let userName = isSender ? '{{ $LoggedUserInfo->name }}' : profileName;

            //             let messageTime = new Date(message.created_at).toLocaleTimeString([], {
            //                 hour: '2-digit',
            //                 minute: '2-digit'
            //             });

            //             let messageHtml = `
            //         <div class="chat-message ${isSender ? 'sender' : 'receiver'}">
            //             <div class="message-avatar">
            //                 <img src="${userAvatar}" class="rounded-circle avatar" alt="User Avatar">
            //             </div>
            //             <div class="message-content">
            //                 <p><strong>${userName}:</strong> ${message.message}</p>
            //                 <div class="timestamp">${messageTime}</div>
            //             </div>
            //         </div>`;
            //         document.getElementById('chatMessageContainer').insertAdjacentHTML('beforeend', messageHtml);
            //     });

            //     // $('#chatMessageContainer').append(messageHtml);
            //             // Scroll to the bottom of the chat container
            //             $('#chatMessageContainer').scrollTop($('#chatMessageContainer')[0].scrollHeight);
            //     },
            //     error: function(xhr, status, error) {
            //         console.error('Error fetching messages:', error);
            //     }
            // });

        });

        $('#messageForm').on('submit', function(e) {
            e.preventDefault();

            let message = $('#messageInput').val().trim();
            let receiverId = $('#receiver_id').val();

            if (message === "") {
                alert("Message cannot be empty.");
                return;
            }

            // Set CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'POST',
                url: '{{ route("send.message") }}', // Ensure route is correct
                data: {
                    message: message,
                    receiver_id: receiverId
                },
                beforeSend: function() {
                    // Disable the send button and change its text to "Sending..."
                    $('#sendMessageButton').text('Sending...').attr('disabled', true);
                },
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        toastr.success(response.message, "Success");
                        $('#messageInput').val(''); // Clear the input
                        let userAvatar = '{{ $LoggedUserInfo->picture ? asset("storage/" . $LoggedUserInfo->picture) : asset("src/assets/images/avatar-default (1).svg") }}';
                        let userName = '{{ $LoggedUserInfo->name }}';

                        let messageTime = new Date().toLocaleTimeString([], {
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                        let messageHtml = `
                    <div class="chat-message sender">
                        <div class="message-avatar">
                            <img src="${userAvatar}" class="rounded-circle avatar" alt="User Avatar">
                        </div>
                        <div class="message-content">
                            <p><strong>${userName}:</strong> ${message}</p>
                            <div class="timestamp">${messageTime}</div>
                        </div>
                    </div>`;
                    // console.log(messageHtml);

                        document.getElementById('chatMessageContainer').insertAdjacentHTML('beforeend', messageHtml);

                        // Scroll to the bottom of the chat container after sending a message
                        $('#chatMessageContainer').scrollTop($('#chatMessageContainer')[0]
                            .scrollHeight);
                    } else {
                        toastr.error(response.message, "Error");
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseJSON.message);
                    toastr.error('Failed to send message', "Error");
                },
                complete: function() {
                    // Re-enable the send button and change its text back to "Send"
                    $('#sendMessageButton').text('Send').attr('disabled', false);
                }
            });
        });
