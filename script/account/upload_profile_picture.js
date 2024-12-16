document.addEventListener('DOMContentLoaded', function() {
    let cropper;
    const uploadImage = document.getElementById('profile_picture');
    const imagePreview = document.getElementById('imagePreview');
    const cropContainer = document.getElementById('crop-container');
    const cropButton = document.getElementById('cropButton');

    uploadImage.addEventListener('change', function(){
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (event) {
                imagePreview.src = event.target.result;
                cropContainer.style.display = 'block';
                cropButton.style.display = 'inline-block';

                cropContainer.style.height = '400px';

                if (cropper) cropper.destroy();
                cropper = new Cropper(imagePreview, {
                    aspectRatio: 1,
                    viewMode: 1,
                });
            };
            reader.readAsDataURL(file);
        };
    });

    cropButton.addEventListener('click', function () {
        if (cropper) {
            const canvas = cropper.getCroppedCanvas({
                width: 1024,
                height: 1024,
            });

            canvas.toBlob(function (blob) {
                const formData = new FormData();
                formData.append('croppedImage', blob);

                fetch('/script/account/upload_profile_picture.php', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        }
    });

});


document.addEventListener('DOMContentLoaded', function() {
    let cropper;
    const uploadImage = document.getElementById('profile_banner');
    const imagePreview = document.getElementById('imagePreview-banner');
    const cropContainer = document.getElementById('crop-container-banner');
    const cropButton = document.getElementById('cropButton-banner');

    uploadImage.addEventListener('change', function(){
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (event) {
                imagePreview.src = event.target.result;
                cropContainer.style.display = 'block';
                cropButton.style.display = 'inline-block';

                cropContainer.style.height = '400px';

                if (cropper) cropper.destroy();
                cropper = new Cropper(imagePreview, {
                    aspectRatio: 4,
                    viewMode: 1,
                });
            };
            reader.readAsDataURL(file);
        };
    });

    cropButton.addEventListener('click', function () {
        if (cropper) {
            const canvas = cropper.getCroppedCanvas({
                width: 1024,
                height: 256,
            });

            canvas.toBlob(function (blob) {
                const formData = new FormData();
                formData.append('croppedImage', blob);

                fetch('/script/account/upload_profile_banner.php', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        }
    });

});
