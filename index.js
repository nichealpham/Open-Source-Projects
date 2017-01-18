// Setup basic express server
var express = require('express');
var path = require('path');
var favicon = require('serve-favicon');
var logger = require('morgan');
var cookieParser = require('cookie-parser');
var bodyParser = require('body-parser');

var app = express();

var server = require('http').createServer(app);
var io = require('socket.io').listen(server);
var port = process.env.PORT || 2000;

// view engine setup
app.set('views', path.join(__dirname, 'views'));
app.set('view engine', 'jade');

// Routing
app.use(logger('dev'));
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: false }));
app.use(cookieParser());
app.use(express.static(path.join(__dirname, 'public')));

// server.listen(port, function () {
//   console.log('Server listening at port %d', port);
// });
server.listen(port, "0.0.0.0", function () {
  console.log('BOM server listening at port %d', port);
});


// catch 404 and forward to error handler
app.use(function(req, res, next) {
  var err = new Error('Not Found');
  err.status = 404;
  next(err);
});

// error handler
app.use(function(err, req, res, next) {
  // set locals, only providing error in development
  res.locals.message = err.message;
  res.locals.error = req.app.get('env') === 'development' ? err : {};

  // render the error page
  res.status(err.status || 500);
  res.render('error');
});

app.use(function(req, res, next) {
  res.header("Access-Control-Allow-Origin", "*");
  res.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
  next();
});

var numUsers = 0;

io.sockets.on('connection', function (socket) {
  console.log("New user connected...");
  var addedUser = false;

  ///////////////////////////////////
    // DATA STREAMMING
  ///////////////////////////////////

  socket.on('init', function () {
      socket.emit('LED', true);
  });
  socket.on('dataFromPhoneToServer', function (respone) {
    io.sockets.emit("dataFromServerToWebsite", respone);
  });

  socket.on("dataFilteredToMobileDeviceButFirstToServer", function (respone) {
    io.sockets.emit("dataFilteredToMobileDeviceSentByServer", respone);
  });

  socket.on('temp', function (temp) {
      socket.broadcast.emit('datasent', temp);
      //console.log(temp);
  });
  socket.on("appFakeNotifyWebToStartToServer", function (respone) {
    io.sockets.emit("appFakeNotifyWebToStartByServer", respone);
  });

  socket.on("appFakeNotifyWebToStopToServer", function (respone) {
    io.sockets.emit("appFakeNotifyWebToStopByServer", respone);
  });

  socket.on('toMobile', function (temp) {
      io.sockets.emit('dataFromHardwareToMobile', {data: temp} );
      console.log(temp);
  });

  // CHAT APP
  // when the client emits 'new message', this listens and executes
  socket.on('new message', function (data) {
    // we tell the client to execute 'new message'
    socket.broadcast.emit('new message', {
      username: socket.username,
      message: data
    });
  });

  // when the client emits 'add user', this listens and executes
  socket.on('add user', function (username) {
    if (addedUser) return;

    // we store the username in the socket session for this client
    socket.username = username;
    ++numUsers;
    addedUser = true;
    socket.emit('login', {
      numUsers: numUsers
    });
    // echo globally (all clients) that a person has connected
    socket.broadcast.emit('user joined', {
      username: socket.username,
      numUsers: numUsers
    });
  });

  // when the client emits 'typing', we broadcast it to others
  socket.on('typing', function () {
    socket.broadcast.emit('typing', {
      username: socket.username
    });
  });

  // when the client emits 'stop typing', we broadcast it to others
  socket.on('stop typing', function () {
    socket.broadcast.emit('stop typing', {
      username: socket.username
    });
  });

  // when the user disconnects.. perform this
  socket.on('disconnect', function () {
    if (addedUser) {
      --numUsers;

      // echo globally that this client has left
      socket.broadcast.emit('user left', {
        username: socket.username,
        numUsers: numUsers
      });
    }
  });
});
module.exports = app;
