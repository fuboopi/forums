


let currentConversationId = null; 



socket.onmessage = function (event) {
    const data = JSON.parse(event.data);
    console.log("New message received:", data);
    updateChatWindow(data);
};



function updateChatWindow(data) {
    const chatBox = document.getElementById("chatBox");
    const messageElement = document.createElement("div");
    messageElement.textContent = `${data.senderName}: ${data.message}`;
    chatBox.appendChild(messageElement);

    
    chatBox.scrollTop = chatBox.scrollHeight;
}


function createNewConversation(userId) {
    console.log("Starting DM with user ID:", userId);

    
    fetch('/script/messages/create_conversation.php', {
        method: 'POST',
        body: new URLSearchParams({ recipientId: userId }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.conversationId) {
            closeNewDM();  
            addConversationToSidebar(userId, data.conversationId);
            loadChat(data.conversationId);  
        } else {
            alert('Error starting the conversation.');
        }
    })
    .catch(error => {
        console.error('Error starting new conversation:', error);
        alert('Error starting the conversation.');
    });
}


function addConversationToSidebar(userId, conversationId) {
    fetch(`/script/messages/get_user_data.php?userId=${userId}`)
        .then(response => response.json())
        .then(data => {
            const sidebar = document.querySelector('.sidebar ul');
            const newConversationItem = document.createElement('li');
            newConversationItem.classList.add('user');
            newConversationItem.onclick = () => loadChat(conversationId);

            newConversationItem.innerHTML = `
                <img src="data:image/jpeg;base64,${data.picture}" alt="User Picture" class="user-picture">
                <span class="user-name">${data.name}</span>
            `;

            sidebar.appendChild(newConversationItem);
        })
        .catch(error => {
            console.error('Error fetching user data:', error);
            alert('Error fetching user data.');
        });
}


function loadChat(conversationId) {
    console.log("Loading chat for conversation ID:", conversationId);
    currentConversationId = conversationId;  
    console.log("Current conversation ID:", currentConversationId);  

    const chatWindow = document.getElementById("chatWindow");
    const messageBar = document.querySelector(".message-bar");
    const chatBox = document.getElementById("chatBox");

    chatWindow.style.display = "block";  
    messageBar.style.display = "flex";   
    chatBox.innerHTML = "<p>Loading...</p>";  

    
    fetch(`/script/messages/get_conversation_messages.php?conversationId=${conversationId}`)
        .then(response => response.json())
        .then(messages => {
            chatBox.innerHTML = '';  
            messages.forEach(message => {
                const messageElement = document.createElement("div");
                messageElement.classList.add("message");
                messageElement.innerHTML = `
                    <strong>${message.senderName}</strong>: ${message.content}
                    <span class="timestamp">${new Date(message.timestamp).toLocaleString()}</span>
                `;
                chatBox.appendChild(messageElement);
            });
        })
        .catch(error => {
            console.error('Error loading messages:', error);
            chatBox.innerHTML = "<p>Error loading messages.</p>";
        });
}





function startNewDM() {
    document.getElementById("newDMInterface").style.display = "flex";  
}


function closeNewDM() {
    document.getElementById("newDMInterface").style.display = "none";  
}


function searchUsers() {
    const searchQuery = document.getElementById("searchBox").value;
    if (searchQuery.length < 3) {
        return;  
    }

    fetch(`/script/messages/search_users.php?query=${searchQuery}`)
        .then(response => response.json())  
        .then(data => {
            const resultsContainer = document.getElementById("userSearchResults");
            resultsContainer.innerHTML = '';  

            if (data.length > 0) {
                data.forEach(user => {
                    const userItem = document.createElement("li");
                    userItem.textContent = user.name;
                    userItem.onclick = () => createNewConversation(user.uid);  
                    resultsContainer.appendChild(userItem);
                });
            } else {
                resultsContainer.innerHTML = "<li>No users found</li>";
            }
        })
        .catch(error => {
            console.error("Error fetching users:", error);
            alert("There was an error processing your request. Please try again later.");
        });
}

