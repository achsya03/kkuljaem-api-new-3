// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
    apiKey: "AIzaSyCOCndwu1sUu8w2FDALq_mTkw7XsZsLKaE",
    authDomain: "kkuljaem-korean-810c1.firebaseapp.com",
    projectId: "kkuljaem-korean-810c1",
    storageBucket: "kkuljaem-korean-810c1.appspot.com",
    messagingSenderId: "435982918944",
    appId: "1:435982918944:web:a16f995018ca6d46fee345",
    measurementId: "G-FD7DWRZ36Y"
  };
  // Initialize Firebase
  firebase.initializeApp(firebaseConfig);
  //firebase.analytics();
  const messaging = firebase.messaging();
      messaging
  .requestPermission()
  .then(function () {
  //MsgElem.innerHTML = "Notification permission granted." 
      console.log("Notification permission granted.");

      // get the token in the form of promise
      return messaging.getToken()
  })
  .then(function(token) {
  // print the token on the HTML page     
  console.log(token);
  
  
  
  })
  .catch(function (err) {
      console.log("Unable to get permission to notify.", err);
  });

  messaging.onMessage(function(payload) {
      console.log(payload);
      var notify;
      notify = new Notification(payload.notification.title,{
          body: payload.notification.body,
          icon: payload.notification.icon,
          tag: "Dummy"
      });
      console.log(payload.notification);
  });

      //firebase.initializeApp(config);
  var database = firebase.database().ref().child("/users/");
  
  database.on('value', function(snapshot) {
      renderUI(snapshot.val());
  });

  // On child added to db
  database.on('child_added', function(data) {
      console.log("Comming");
      if(Notification.permission!=='default'){
          var notify;
          
          notify= new Notification('CodeWife - '+data.val().username,{
              'body': data.val().message,
              'icon': 'bell.png',
              'tag': data.getKey()
          });
          notify.onclick = function(){
              alert(this.tag);
          }
      }else{
          alert('Please allow the notification first');
      }
  });

  self.addEventListener('notificationclick', function(event) {       
      event.notification.close();
  });