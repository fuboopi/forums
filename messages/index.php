<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/setup/config.php');
include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');


$conversations = [];


$sql = "SELECT users.uid, users.name, users.picture FROM users
        JOIN messages ON (messages.sender_id = users.uid OR messages.receiver_id = users.uid)
        WHERE messages.sender_id = ? OR messages.receiver_id = ?
        GROUP BY users.uid";
$stmt = $link->prepare($sql);
$stmt->bind_param("ii", $_SESSION['uid'], $_SESSION['uid']);
$stmt->execute();
$result = $stmt->get_result();
$users = [];
while ($row = $result->fetch_assoc()) {
    
    $row['picture'] = base64_encode($row['picture']);
    $users[] = $row;
}


$sql = "SELECT c.conversation_id, u1.uid AS user_1_id, u2.uid AS user_2_id, 
               u1.name AS user_1_name, u2.name AS user_2_name, 
               u1.picture_dir AS user_1_picture, u2.picture_dir AS user_2_picture 
        FROM conversations c
        JOIN users u1 ON c.user_1_id = u1.uid
        JOIN users u2 ON c.user_2_id = u2.uid
        WHERE c.user_1_id = ? OR c.user_2_id = ?";
$stmt = $link->prepare($sql);
$stmt->bind_param("ii", $_SESSION['uid'], $_SESSION['uid']);
$stmt->execute();
$result = $stmt->get_result();


while ($row = $result->fetch_assoc()) {
    
    $conversations[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Direct Messages | <?php echo $site_name?></title>
    <link rel="stylesheet" href="<?php echo $stylesheet; ?>">
    <link rel="stylesheet" href="/style/messages.css"> 
    <link rel="stylesheet" href="/style/header.css">

    <script src="https://cdn.socket.io/4.0.1/socket.io.min.js"></script>
</head>
<body>
    <div class="messages-container">
        <div class="sidebar">
            <button onclick="startNewDM()">New DM</button>
            <ul>
                <?php if (empty($conversations)): ?>
                    <li>It's all lonely here...</li>
                <?php else: ?>
                    <?php foreach ($conversations as $conversation): ?>
                        <?php 
                        
                        if ($_SESSION['uid'] == $conversation['user_1_id']) {
                            
                            $recipient_name = $conversation['user_2_name'];
                            $recipient_picture = $conversation['user_2_picture'];
                        } else {
                            
                            $recipient_name = $conversation['user_1_name'];
                            $recipient_picture = $conversation['user_1_picture'];
                        }
                        ?>
                        <li class="user" onclick="loadChat(<?php echo $conversation['conversation_id']; ?>)">
                            <img src="<?php echo $recipient_picture; ?>" alt="Recipient Picture" class="user-picture">
                            <span class="user-name"><?php echo $recipient_name; ?></span>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>

        <div class="chat-window" id="chatWindow" style="display: none;">
            <div id="chatBox"></div>

            <div class="message-bar">
                <input type="text" id="messageInput" placeholder="Message...">
                <button onclick="sendMessage()">Send</button>
            </div>
        </div>

        <div id="newDMInterface" class="popup" style="display: none;">
            <div class="popup-content">
                <h3>Search for User...</h3>
                <input type="text" id="searchBox" placeholder="Search for users" onkeyup="searchUsers()">
                <ul id="userSearchResults"></ul>
                <button class="cancel-button" onclick="closeNewDM()">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        const userId = <?php echo isset($_SESSION['uid']) ? $_SESSION['uid'] : 'null'; ?>;
        const socket = new WebSocket(`wss://forums.fuboopi.com:3000?userId=${userId}`);
    </script>
    <script src="/messages/app.js"></script>
    <script>
    
    if (userId === null) {
        alert("You must be logged in to send messages.");
    }


    
    socket.onmessage = function (event) {
        const data = JSON.parse(event.data);
        console.log("New message received:", data);
        updateChatWindow(data);
    };

    function getRecipientIdFromConversation(conversationId, currentUserId) {
        const conversation = <?php echo json_encode($conversations); ?>.find(convo => convo.conversation_id === conversationId);

        if (!conversation) {
            console.log("Conversation not found!");
            return null;
        }

        
        return conversation.user_1_id === currentUserId ? conversation.user_2_id : conversation.user_1_id;
    }

    
    function sendMessage() {
        const messageInput = document.getElementById("messageInput");
        const message = messageInput.value.trim();

        if (message && currentConversationId) {
            const conversationId = currentConversationId;  

            
            const recipientId = getRecipientIdFromConversation(conversationId, userId); 

            console.log("Send message called:", message, "From:", userId, "To:", recipientId, "ConvID:", conversationId); 

            
            socket.send(JSON.stringify({
                event: 'sendMessage',
                data: {
                    recipientId: recipientId,   
                    senderId: userId,           
                    conversationId: conversationId, 
                    message: message            
                }
            }));


            
            messageInput.value = '';  
        } else {
            console.log("Message is empty or no conversation selected.");
        }
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
                alert("Error processing your request. Please try again later.");
            });
    }

    function createNewConversation(userId) {
        console.log("Starting DM with user ID:", userId);

        fetch('/script/messages/create_conversation.php', {
            method: 'POST',
            body: new URLSearchParams({
                recipientId: userId
            }),
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.conversationId) {
                console.log('Conversation created:', data.conversationId);

                
                fetchConversations();

                
                loadChat(data.conversationId);

                closeNewDM();  
            } else {
                alert('Error starting the conversation.');
            }
        })
        .catch(error => {
            console.error('Error starting new conversation:', error);
            alert('Error starting the conversation.');
        });
    }

    async function fetchConversations() {
        try {
            const response = await fetch('/script/messages/get_conversations.php');
            const data = await response.json();

            const dmList = document.querySelector('.sidebar ul');
            dmList.innerHTML = '';  

            if (data.conversations && data.conversations.length > 0) {
                const fragment = document.createDocumentFragment();

                data.conversations.forEach(conversation => {
                    const newDM = document.createElement("li");
                    newDM.classList.add("user");
                    newDM.dataset.conversationId = conversation.conversation_id;
                    newDM.onclick = () => loadChat(conversation.conversation_id);
                    newDM.innerHTML = `
                        <img src="data:image/jpeg;base64,${conversation.recipientPicture}" alt="Recipient Picture" class="user-picture">
                        <span class="user-name">${conversation.recipientName}</span>
                    `;
                    fragment.appendChild(newDM);
                });

                dmList.appendChild(fragment);
            } else {
                dmList.innerHTML = '<li>No DMs yet.</li>';
            }
        } catch (error) {
            console.error("Error fetching conversations:", error);
            alert("Error processing your request. Please try again later.");
        }
    }

    function updateChatWindow(data) {
        
        const chatBox = document.getElementById("chatBox");
        const messageElement = document.createElement('p');
        messageElement.textContent = data.message;
        chatBox.appendChild(messageElement);
    }
</script>
</body>
</html>
