const WebSocket = require('ws');
const https = require('https');
const fs = require('fs');
const mysql = require('mysql2');

// Read SSL certificate files
const serverOptions = {
    key: fs.readFileSync('/etc/letsencrypt/live/forums.fuboopi.com/privkey.pem'),
    cert: fs.readFileSync('/etc/letsencrypt/live/forums.fuboopi.com/cert.pem')
};

// Create an HTTPS server
const server = https.createServer(serverOptions);

// Create a WebSocket server on the HTTPS server
const wss = new WebSocket.Server({ server });

// MySQL database connection
const db = mysql.createConnection({
    host: 'localhost',
    user: 'forums',  // Replace with your DB username
    password: 'Marksclub_2005',  // Replace with your DB password
    database: 'forums'  // Replace with your DB name
});

// Store connected clients (Map: userId -> WebSocket)
const clients = new Map();

wss.on('connection', (ws, req) => {
    // Get user ID from the query parameter
    const urlParams = new URLSearchParams(req.url.split('?')[1]);
    const userId = urlParams.get('userId');
    
    if (!userId) {
        console.log('No userId provided, closing connection');
        ws.close();  // Close the connection if no userId is provided
        return;
    }

    // Register the WebSocket client with the user ID
    clients.set(userId, ws);
    console.log(`User ${userId} connected`);

    // Log the clients Map after registering a new connection
    console.log('Current connected clients:', Array.from(clients.keys()));

    // When the client sends a message, process it
    ws.on('message', (message) => {
        console.log(`Received message from ${userId}: ${message}`);

        try {
            const parsedMessage = JSON.parse(message);
            
            // Log the parsed message to inspect its structure
            console.log("Parsed message:", parsedMessage);

            // Handle sendMessage event
            if (parsedMessage.event === 'sendMessage') {
                const recipientId = parsedMessage.data.recipientId;
                const textMessage = parsedMessage.data.message;

                // Ensure recipientId is treated as a string for comparison with map keys
                const recipientIdStr = String(recipientId);

                if (clients.has(recipientIdStr)) {
                    const recipientSocket = clients.get(recipientIdStr);

                    // Check if the recipient's WebSocket is open before sending the message
                    if (recipientSocket.readyState === WebSocket.OPEN) {
                        recipientSocket.send(JSON.stringify({
                            senderId: userId,
                            message: textMessage
                        }));
                        console.log(`Message sent to recipient ${recipientId}`);
                        
                        // Save the message to MySQL
                        const query = 'INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)';
                        db.execute(query, [userId, recipientId, textMessage], (err, results) => {
                            if (err) {
                                console.error('Error saving message to database:', err);
                            } else {
                                console.log('Message saved to database:', results);
                            }
                        });
                    } else {
                        console.log(`Recipient ${recipientId}'s connection is not open.`);
                    }
                } else {
                    console.log(`Recipient ${recipientId} is not connected.`);
                }

            } else {
                console.log('Unknown event:', parsedMessage.event);
            }
        } catch (error) {
            console.error('Error parsing message:', error);
        }
    });

    // When the WebSocket connection closes, remove the client from the map
    ws.on('close', () => {
        clients.delete(userId);
        console.log(`User ${userId} disconnected`);
        console.log('Current connected clients:', Array.from(clients.keys()));
    });

    // Optionally: Listen for WebSocket errors
    ws.on('error', (error) => {
        console.error(`WebSocket error for user ${userId}:`, error);
    });
});

// Start the WebSocket server on port 3000 using HTTPS (wss://)
server.listen(3000, () => {
    console.log('WebSocket server is running on wss://localhost:3000');
});
