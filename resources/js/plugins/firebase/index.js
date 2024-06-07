import { initializeApp } from "firebase/app";
import { getMessaging, getToken, onMessage } from "firebase/messaging";
import { firebaseConfig } from './config';

initializeApp(firebaseConfig);

// Get registration token. Initially this makes a network call, once retrieved
// subsequent calls to getToken will return from cache.
const messaging = getMessaging();

const getDeviceToken = permission => {
  if(permission === 'denied') return;

  return getToken(messaging, { vapidKey: 'BAXSZe7HsWFpvODcq4TVITSiTy3YegexTVdqnIan3_bR4aZ_9n2w_iM2ptHokXK5iuigjp3GiKVhxXHm2u_Q49A' });
}

export const storeUserDeviceToken = () => {
  if(Notification.permission === 'granted') 
    getDeviceToken().then(token => {
      if(token) {
        console.log('Token:', token);
        // Send the token to your server if necessary
      } else {
        console.log('No registration token available. Request permission to generate one.');
      }
    });
  else if(Notification.permission === 'default') 
    Notification.requestPermission().then(getDeviceToken).then(token => {
      if(token) {
        console.log('Token:', token);
        // Send the token to your server if necessary
      } else {
        console.log('No registration token available. Request permission to generate one.');
      }
    });
}

onMessage(messaging, payload => {
  console.log('Message received. ', payload);
});
