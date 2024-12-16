document.addEventListener('DOMContentLoaded', function() {
    let deletePost = document.getElementsByClassName('delete-post');
    let editPost = document.getElementsByClassName('edit-post');
    
    for (let deletePostClass of deletePost) {
        deletePostClass.addEventListener('click', function() {
            let postId = deletePostClass.id.replace("delete-", "");
            const data = {
                type: "delete",
                post_id: postId
            };
            fetch('/script/forums/post_options.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                location.reload();
            });
        });
    };
    for (let editPostClass of editPost) {
        editPostClass.addEventListener('click', function() {
            let postId = editPostClass.id.replace("edit-", "");
            location.href = '/forums/edit_post?post_id=' + postId;
        });
    };
});