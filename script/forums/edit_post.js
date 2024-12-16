document.addEventListener('DOMContentLoaded', function(){
    let input = document.getElementById('edit-input');
    let preview = document.getElementById('preview-content');
    input.addEventListener('input', function(){
        let formattedText = input.value.replace(/\n/g, "<br>");
        preview.innerHTML = formattedText;
    });
});