<!doctype html>
<html>
<head>
    <title>Socket.IO chat</title>
</head>
<body>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.2.0/socket.io.js"></script>
    <script>
        var socket = io('127.0.0.1:1900');
        console.log('أدخل برجلك  اليمين');
        socket.emit('user-connected', {
            "room_id": "1"
            , "api_token": "17MICkRN4E3r15Bj"
        });
        socket.emit('send-message', {
            "message": "Nour Muhammed"
            , "file_type": "text"
            , "api_token": "17MICkRN4E3r15Bj"
            , "countryId": "178"
            , "room_id": "1"
            , "duration": 0
            , "file": ""
        });
        // function emitSocket() {
        //
        // }

    </script>
</body>
</html>
