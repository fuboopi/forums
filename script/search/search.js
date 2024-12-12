document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('search_forums').addEventListener('click', function (e) {
        e.preventDefault();
        document.getElementById('search_type').value = 'forums';
        document.getElementById('search_form').submit();
    });
    document.getElementById('search_users').addEventListener('click', function (e) {
        e.preventDefault();
        document.getElementById('search_type').value = 'users';
        document.getElementById('search_form').submit();
    });
    document.getElementById('search_box').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            window.location.href = '/search/?search=' + encodeURIComponent(this.value) + '&search_type=' + encodeURIComponent(document.getElementById('search_type').value);
        }
    });
})
