<!doctype html>
<html>
<head>
    <title>Socket.IO chat</title>
</head>
<body>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.2.0/socket.io.js"></script>
    <script>
        var socket = io(':1900',{secure: true});
                console.log(socket);
                $('form').submit(function(e) {
                    var mes = $('#m').val();
                    e.preventDefault(); // prevents page reloading
                    socket.emit('user-connected',{"room_id":"1","api_token":"121TfvtEBxXHw2YzWG"});
                    socket.emit('send-message',{"lang":"ar","message":'ohjoijhioj',"file_type":"text","api_token":"121TfvtEBxXHw2YzWG","countryId":"178","room_id":"1","duration":0,"file":""});
                    $('#m').val('');
                });
        // function emitSocket() {
        //
        // }

    </script>
</body>
</html>
