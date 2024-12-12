document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('search_forums').addEventListener('click', function (e) {
        e.preventDefault(); // Prevent the form from submitting immediately
        document.getElementById('search_type').value = 'forums';
        document.getElementById('search_form').submit(); // Submit the form manually
    });
    document.getElementById('search_users').addEventListener('click', function (e) {
        e.preventDefault(); // Prevent the form from submitting immediately
        document.getElementById('search_type').value = 'users';
        document.getElementById('search_form').submit(); // Submit the form manually
    });
    document.getElementById('search_box').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            window.location.href = '/search/?search=' + encodeURIComponent(this.value) + '&search_type=' + encodeURIComponent(document.getElementById('search_type').value);
        }
    });
})
