// Import the functions you need from the SDKs you need
importScripts('https://www.gstatic.com/firebasejs/9.6.7/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/9.6.7/firebase-messaging.js');
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
firebase.initializeApp({
  apiKey: "AIzaSyBkZk5VhPa56SqC3tKZxjzp4vRjvDedAjM",
  authDomain: "unimas-web-application.firebaseapp.com",
  projectId: "unimas-web-application",
  storageBucket: "unimas-web-application.appspot.com",
  messagingSenderId: "110082600798",
  appId: "1:110082600798:web:810b515248279c850ba9cf",
  measurementId: "G-XMR9Y42QR4"
})


const messaging = firebase.messaging();

messaging.setBackgroundMessageHandler(function(payload) {
  console.log('[firebase-messaging-sw.js] Received background message ', payload);
  // Customize notification here
  const notificationTitle = 'Background Message Title';
  const notificationOptions = {
    body: 'Background Message body.',
    icon: '/firebase-logo.png'
  };

  return self.registration.showNotification(notificationTitle,
    notificationOptions);
}


);

messaging.requestPermission().then(() => {
  console.log('Notification permission granted.');
  // TODO: Save the token or handle notifications
}).catch((err) => {
  console.log('Unable to get permission to notify.', err);
});

