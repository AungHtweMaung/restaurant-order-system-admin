function renderComments(comments, container) {
    comments.forEach(comment => {
        const li = document.createElement('li');
        li.className = 'mb-4 border-bottom pb-2';

        // Create the comment HTML
        const commentHtml = `
            <div class="d-flex align-items-center mb-1">
                <img src="/src/assets/images/default-user-image.svg" width="24px" alt="" class="me-2">
                <strong>${comment.user.name}</strong>
            </div>
            <div>${comment.content}</div>
            <a href="javascript:void(0);" class="reply-btn" data-comment-id="${comment.id}">Reply</a>
            <form action="/social-posts/${comment.social_post_id}/comments" method="POST" class="reply-form mt-2" data-parent-id="${comment.id}" style="display:none;">
                <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
                <div class="mb-3">
                    <textarea name="content" class="form-control" rows="2" placeholder="Write your reply..." required></textarea>
                    <input type="hidden" name="parent_comment_id" value="${comment.id}">
                </div>
                <button type="submit" class="btn btn-sm btn-primary">Post Reply</button>
                <button type="button" class="btn btn-sm btn-secondary cancel-reply-btn ms-2">Cancel</button>
            </form>
        `;

        li.innerHTML = commentHtml;
        container.append(li);

        // Render replies recursively
        if (comment.replies && comment.replies.length > 0) {
            // Removed "View Replies" link to show all replies directly
            const repliesUl = document.createElement('ul');
            repliesUl.className = 'list-unstyled ms-4 replies-list';
            repliesUl.setAttribute('data-comment-id', comment.id);
            li.appendChild(repliesUl);
            renderComments(comment.replies, $(repliesUl));
        }
    });
}

$(document).ready(function () {
    $('.social-post-like-icon').on('click', function () {
        var likeIcon = $(this);
        var socialPostId = $(this).data('social-post-id');
        // console.log(socialPostId);
        var url = $(this).data('like-url');

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                post_id: socialPostId
            },
            success: function (response) {

                if (response.liked) {
                    likeIcon.removeClass('fa-regular').addClass('fa-solid');
                    $('#like-count-' + socialPostId).text(response.like_count);
                } else {
                    likeIcon.removeClass('fa-solid').addClass('fa-regular');
                    $('#like-count-' + socialPostId).text(response.like_count);

                }
            },
            error: function (xhr) {
                console.error('Error liking post:', xhr);
            }
        });


    });

    $('.comment-store').on('submit', function (e) {
        e.preventDefault();
        console.log('Main comment form submitted');

        var form = $(this);
        var url = form.attr('action');
        var commentContent = form.find('textarea[name="content"]').val();

        console.log('URL:', url);
        console.log('Content:', commentContent);

        if (!commentContent.trim()) {
            alert('Please enter a comment');
            return;
        }

        // Disable submit button to prevent double submission
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).text('Posting...');

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                content: commentContent
            },
            success: function (response) {
                console.log('Success response:', response);

                // Re-enable submit button
                submitBtn.prop('disabled', false).text('Post Comment');

                // Clear the textarea after submission
                form.find('textarea[name="content"]').val('');

                // Display comments from response.comments
                if (response.comments) {
                    $('#comments-body').html(''); // Clear existing comments
                    renderComments(response.comments.filter(c => !c.parent_comment_id), $('#comments-body'));
                    $('.comments-count').text(response.comments.length);
                }

                // Show success message
                if (response.success) {
                    console.log('Comment posted successfully');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error submitting comment:', xhr);
                console.error('Status:', status);
                console.error('Error:', error);

                // Re-enable submit button
                submitBtn.prop('disabled', false).text('Post Comment');

                // Show error message
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    alert('Error: ' + Object.values(xhr.responseJSON.errors).flat().join(', '));
                } else {
                    alert('Error posting comment. Please try again.');
                }
            }
        });

    });

    // New code for reply button toggle
    $(document).on('click', '.reply-btn', function () {
        var commentId = $(this).data('comment-id');
        var form = $('.reply-form[data-parent-id="' + commentId + '"]');
        if (form.is(':visible')) {
            form.hide();
        } else {
            $('.reply-form').hide(); // hide other forms
            form.show();
        }
    });

    // Cancel reply button
    $(document).on('click', '.cancel-reply-btn', function () {
        $(this).closest('.reply-form').hide();
    });



    // Handle reply form submission with AJAX
    $(document).on('submit', '.reply-form', function (e) {
        e.preventDefault();
        console.log('Reply form submitted');

        var form = $(this);
        var url = form.attr('action');
        var commentContent = form.find('textarea[name="content"]').val();
        var parentCommentId = form.find('input[name="parent_comment_id"]').val();

        console.log('Reply URL:', url);
        console.log('Reply Content:', commentContent);
        console.log('Parent Comment ID:', parentCommentId);

        if (!commentContent.trim()) {
            alert('Please enter a reply');
            return;
        }

        // Disable submit button to prevent double submission
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).text('Posting...');

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                content: commentContent,
                parent_comment_id: parentCommentId
            },
            success: function (response) {
                console.log('Reply success response:', response);

                // Re-enable submit button
                submitBtn.prop('disabled', false).text('Post Reply');

                // Clear the textarea and hide form
                form.find('textarea[name="content"]').val('');
                form.hide();

                // Re-render all comments
                if (response.comments) {
                    $('#comments-body').html(''); // Clear existing comments
                    renderComments(response.comments.filter(c => !c.parent_comment_id), $('#comments-body'));
                    $('.comments-count').text(response.comments.length);
                }

                // Show success message
                if (response.success) {
                    console.log('Reply posted successfully');
                }
            },
            error: function (xhr, status, error) {

                // Re-enable submit button
                submitBtn.prop('disabled', false).text('Post Reply');

                // Show error message
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    alert('Error: ' + Object.values(xhr.responseJSON.errors).flat().join(', '));
                } else {
                    alert('Error posting reply. Please try again.');
                }
            }
        });
    });

});
