document.addEventListener('DOMContentLoaded', function () {
    let upvote = document.getElementsByClassName('upvote');
    let downvote = document.getElementsByClassName('downvote');

    for (let upvoteClass of upvote) {
        upvoteClass.addEventListener('click', function () {
        let secondClass = (upvoteClass && upvoteClass.classList.contains('selected')) ? 'selected' : undefined;
        let type = (secondClass === undefined) ? "upvote" : null;

        const data = {
            post_id: upvoteClass.id,
            type: type
        };

            console.log('Upvote Data:', data);

            fetch('/script/forums/vote.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(responseData => {
                console.log('Response from PHP:', responseData);
                if (type == "upvote") {
                    upvoteClass.classList.add('selected');
                    let other = upvoteClass.nextElementSibling;
                    if (other && other.classList.contains('selected')) {
                        other.classList.remove('selected');
                        let counter = parseInt(document.getElementById(`vote-count${upvoteClass.id}`).innerHTML);
                        console.log(counter);
                        document.getElementById(`vote-count${upvoteClass.id}`).innerHTML = counter + 1;
                    }
                    let counter = parseInt(document.getElementById(`vote-count${upvoteClass.id}`).innerHTML);
                    console.log(counter);
                    document.getElementById(`vote-count${upvoteClass.id}`).innerHTML = counter + 1;
                } else if (type == null) {
                    upvoteClass.classList.remove('selected');
                    let counter = parseInt(document.getElementById(`vote-count${upvoteClass.id}`).innerHTML);
                    console.log(counter);
                    document.getElementById(`vote-count${upvoteClass.id}`).innerHTML = counter - 1;
                };
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }

    for (let downvoteClass of downvote) {
        downvoteClass.addEventListener('click', function () {
            let secondClass = (downvoteClass && downvoteClass.classList.contains('selected')) ? 'selected' : undefined;
            let type = (secondClass === undefined) ? "downvote" : null;
            const data = {
                post_id: downvoteClass.id,
                type: type
            };

            console.log('Downvote Data:', data);

            fetch('/script/forums/vote.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(responseData => {
                console.log('Response from PHP:', responseData);
                if (type == "downvote") {
                    downvoteClass.classList.add('selected');
                    let other = downvoteClass.previousElementSibling;
                    if (other && other.classList.contains('selected')) {
                        other.classList.remove('selected');
                        let counter = parseInt(document.getElementById(`vote-count${downvoteClass.id}`).innerHTML);
                        console.log(counter);
                        document.getElementById(`vote-count${downvoteClass.id}`).innerHTML = counter - 1;
                    }
                    let counter = parseInt(document.getElementById(`vote-count${downvoteClass.id}`).innerHTML);
                    console.log(counter);
                    document.getElementById(`vote-count${downvoteClass.id}`).innerHTML = counter - 1;
                } else if (type == null) {
                    downvoteClass.classList.remove('selected');
                    let counter = parseInt(document.getElementById(`vote-count${downvoteClass.id}`).innerHTML);
                    console.log(counter);
                    document.getElementById(`vote-count${downvoteClass.id}`).innerHTML = counter + 1;
                };
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }
});
