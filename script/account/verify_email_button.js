document.addEventListener('DOMContentLoaded', function() {
    const verifyButton = document.getElementById('email-verify');
    const email = document.getElementById('email');

    verifyButton.addEventListener('click', function(){
        const data = {
            email: email.value
        };

        fetch('/script/account/verify_email_send.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.text())
        .then(data => {
            //alert(data);
        })
        .catch(error => {
            console.error('Error:', error);
        });
    })
})