var app = require('express')();
// var server = require('https').Server(app);
//
// var fs = require('fs');
// // Certificate were created from openssl command
// const pkey = fs.readFileSync('/home/beautyapp/chatSocket/key.pem', 'utf8');
// const pcert = fs.readFileSync('/home/beautyapp/chatSocket/cert.pem', 'utf8');
// const pca = fs.readFileSync('/home/beautyapp/chatSocket/csr.pem', 'utf8');

// const credentials = {
//     key: pkey,
//     cert: pcert,
//     ca: pca
// };

var http = require('http');


var server = http.createServer(app);
// var options = {
//     serveClient: false,
//     origins: '*:*',
//     transports: ['polling'],
//     pingInterval: 10000,
//     pingTimeout: 5000,
//     cookie: false
// };
// var io = require('socket.io')(server, options);
var io = require('socket.io')(server);

// server port;
const PORT = 1900;
// to use axios
const axios = require('axios');

// default home directory;
// app.use('/chat', (req, res, next) => res.send('<h1>Hello, World!</h1>'));

// users list
var users = [];

// listen on PORT;
server.listen(PORT, () => console.log('Server started on port : ' + PORT));

io.on('connection', function(socket) {
    console.log('لا حول والا قوة الا بالله  العلي  العظيم');

    let userConnected = function(data) {
        var config = {
            headers: {
                'Accept-Language': "ar",
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + data.api_token,
            }
        };
        axios.post('http://127.0.0.1:8000/api/v1/connect-room', data, config).then(
            function(response) {
                socket.phone = response.data.user_phone;
                users[socket.phone] = '';
                users[socket.phone] = [socket.id, data.room_id];
            }
        ).catch(function(error) {
            console.log(JSON.stringify(error));
        });
    };

    let userDisconnected = function() {
        var config = {
            headers: {
                'Accept-Language': "ar",
                'Content-Type': 'application/json',
            }
        };
        var room;
        if (users[socket.phone]) {
            room = users[socket.phone][1];
        } else {
            room = null;
        }
        console.log(socket.phone)
        var data = {
            'phone': socket.phone,
            'room_id': room
        };
        console.log(data)

        axios.post('http://127.0.0.1:8000/api/v1/disconnect-room', data, config).then(
            function(response) {
                console.log('disconnected successfully');
                // console.log(JSON.stringify(response.data));
            }
        ).catch(function(error) {
            console.log(JSON.stringify());
        });
        // remove user from array
        users.splice(users.indexOf(socket.phone), 1);

        io.emit('disconnect', {
            phone: socket.phone
        });
        console.log(users);
    };
    // listen on any user connected to socket;
    socket.on('user-connected', userConnected);
    // display connected user socket id;
    console.log(`${socket.id} connected`);
    // listen on anyone send a message;
    socket.on('send-message', (data) => {

        var config = {
            headers: {
                'Accept-Language': "ar",
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + data.api_token,
            }
        };
        // console.log('data is : ',data);
        axios.post('http://127.0.0.1:8000/api/v1/send-message', data, config).then(
            function(response) {
                io.emit('send-message', JSON.stringify(response.data.data));
            }
        ).catch(function(error) {
            console.log(JSON.stringify(error));
        });
    });

    socket.on('chat message', (data) => {
        io.emit('chat message', {
            "msg": data.msg,
            "room_id": data.room_id,
            "img": data.img
        });
    });
    // listen on disconnected user;
    socket.on('disconnect', userDisconnected);
});