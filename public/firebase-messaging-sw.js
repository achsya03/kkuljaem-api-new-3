importScripts('https://www.gstatic.com/firebasejs/3.9.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/3.9.0/firebase-messaging.js');

const firebaseConfig = {
  apiKey: "AIzaSyCOCndwu1sUu8w2FDALq_mTkw7XsZsLKaE",
  authDomain: "kkuljaem-korean-810c1.firebaseapp.com",
  projectId: "kkuljaem-korean-810c1",
  storageBucket: "kkuljaem-korean-810c1.appspot.com",
  messagingSenderId: "435982918944",
  appId: "1:435982918944:web:a16f995018ca6d46fee345",
  measurementId: "G-FD7DWRZ36Y"
};
// Initialize the Firebase app in the service worker by passing in the
// messagingSenderId.
firebase.initializeApp(firebaseConfig);

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();

messaging.setBackgroundMessageHandler(function(payload) {
  console.log('[firebase-messaging-sw.js] Received background message ', payload);
  // Customize notification here
  const notificationTitle = 'Background Message Title';
  const notificationOptions = {
    body: 'Background Message body.',
    icon: 'https://images.theconversation.com/files/93616/original/image-20150902-6700-t2axrz.jpg' //your logo here
  };

  return self.registration.showNotification(notificationTitle,
      notificationOptions);
});