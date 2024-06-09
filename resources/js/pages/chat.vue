
<script setup>
import FollowingList from '@/components/Post/FollowingList.vue';
import { useAuthStore } from '@/plugins/store/AuthStore';
import axios from 'axios';
import 'emoji-picker-element';
import { debounce } from 'lodash';
import { ref } from 'vue';
import { useRoute } from 'vue-router';
import { errorMessages } from 'vue/compiler-sfc';

import chatBackground from '/resources/images/avatars/chatbackground.png';

const props = defineProps({
  newChatUser: {
    type: Object,
    required: false
  }
});

const route = useRoute();
const store = useAuthStore();
const newMessage = ref('');
const messages = ref([]);
const messagesShowed = ref([]);
const receiver = ref({});
const clickedMessage = ref({});
const messageClicked = ref(false);
const searchUser = ref('');
const messagePerPage = ref(10);
let page = 1;
const user = ref({});
const showLoading = ref(false);
const showEmoji = ref(false);
const showFile = ref(false);
const images = ref([]);
const isError = ref(false);
const isSuccess = ref(false);
const followingUsers = ref([]);
const successMessages = ref('');
const openFollowedUsersDialog = ref(false);
const currentUserID = ref('');
const hasNewMessage = ref(false);


const addEmoji = (event) => {
  // Access the emoji data from the event object
  const emoji = event.detail.unicode;
  newMessage.value += emoji;

};

const getFollowingUsers = () => {
  axios.get('/api/users/get-following')
    .then(response => {
      followingUsers.value = response.data.data;
    })
    .catch(error => {
      console.log(error)
    })
}


// get the authenticated user
const getUser = async () => {
  try {
    const token = localStorage.getItem('token');
    
    if (!token) {
      throw new Error('Token not found');
    }

    const response = await axios.get('/api/auth/get-user-by-token', {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    user.value = response.data.user;
    currentUserID.value = user.value.user_id;
  
    
  } catch (error) {

    console.error(error);
  }
};

// fetch messages of all message related to the authenticated user
const fetchMessages = debounce(() => {
  let requestURL = '/api/chat/messages?page='+page+'&per_page='+messagePerPage.value;
  if(searchUser.value && searchUser.value.length > 2 && searchUser.value !== null && searchUser.value !== '') {
    requestURL += '&search_user=' + searchUser.value;
  }

  axios.get(requestURL)
    .then(({data}) => {
      messages.value = data.data;

      messages.value.forEach(message => {
        if (message.sender.avatar) {
          message.sender.avatar = 'http://127.0.0.1:8000/storage/' + message.sender.avatar;
        }
        if (message.receiver.avatar) {
          message.receiver.avatar = 'http://127.0.0.1:8000/storage/' + message.receiver.avatar;
        }
        if(message.pictures){
          message.pictures.forEach(picture => {
            picture.picture_path = 'http://127.0.0.1:8000/storage/' + picture.picture_path;
          });
        }
        
      });

  if(props.newChatUser !== null && props.newChatUser !== undefined){
      messageClicked.value = true;
      clickedMessage.value = props.newChatUser;    // so that can know which user is clicked
      receiver.value = props.newChatUser;
      messagesShowed.value = messages.value.filter(message => message.sender.user_id === props.newChatUser.user_id || message.receiver.user_id === props.newChatUser.user_id);
      if(messagesShowed.value.length < 1){
        newMessage.value = 'Hi, I am ' + store.user.username + '. Let\'s start chatting';
      }else{
        setTimeout(() => {
        const chat = document.querySelector('.message');
        chat.scrollTop = chat.scrollHeight;
      }, 100);
      }
      
    }


    // show the messages of the clicked user
    if ((messageClicked.value || showLoading.value === true) && clickedMessage.value.user !== undefined) {
      messagesShowed.value =Object.assign([], messages.value.filter(message => message.sender.user_id === clickedMessage.value.user.user_id || message.receiver.user_id === clickedMessage.value.user.user_id));
      setTimeout(() => {
      const chat = document.querySelector('.message');
      chat.scrollTop = chat.scrollHeight;
      }, 100);
    }

  })
  .catch(error => {
    console.log(error);
  });

  
}, 800);

// show the avatar of the user
const showAvatar = (index) => {
  if (index === 0) {
    return true; // Show avatar for the first message
  }

  const prevMessage = messagesShowed.value[index - 1];
  const currentMessage = messagesShowed.value[index];

  return prevMessage.sender.user_id !== currentMessage.sender.user_id || !prevMessage.sender.avatar;
};


// the list of users with the latest message
const latestUserMessages = computed(() => {
  const userMap = new Map(); // Map to store latest message for each user
  messages.value.forEach(message => {
    const userId = message.sender.user_id !== store.user.user_id ? message.sender.user_id : message.receiver.user_id;
    if (!userMap.has(userId) || message.created_at > userMap.get(userId).created_at) {
      userMap.set(userId, { user: message.sender.user_id !== store.user.user_id ? message.sender : message.receiver, message: message.message, created_at: message.created_at });
    }
  });
  // check also whether there is a picture in the last message or not, if there is a picture, then isPicture will be true
  messages.value.forEach(message => {
    const userId = message.sender.user_id !== store.user.user_id ? message.sender.user_id : message.receiver.user_id;
    if (userMap.has(userId) && message.created_at === userMap.get(userId).created_at) {
      if(message.pictures && message.pictures.length > 0){
        userMap.get(userId).isPicture = true;
      }else{
        userMap.get(userId).isPicture = false;
      }
    }
  });

  // Check also whether there is a "Location: " in the last message or not, if there is a "Location: ", then isLocation will be true
  messages.value.forEach(message => {
    const userId = message.sender.user_id !== store.user.user_id ? message.sender.user_id : message.receiver.user_id;
    if (userMap.has(userId) && message.created_at === userMap.get(userId).created_at) {
      if(message.message && message.message.includes('Location:')){
        userMap.get(userId).isLocation = true;
      }else{
        userMap.get(userId).isLocation = false;
      }
    }
  });

  //check also whether the message is read or not through the status of the message, if the status is 'unread', then notRead will be true and calculate the number of unread messages
  // make sure the notRead message is belong to the receiver side only, not the sender side
  messages.value.forEach(message => {
    const userId = message.sender.user_id !== store.user.user_id ? message.sender.user_id : message.receiver.user_id;
    if (userMap.has(userId) && message.created_at === userMap.get(userId).created_at) {
      if(message.status === 'unread' && message.receiver.user_id === store.user.user_id){
        userMap.get(userId).notRead = true;
      }else{
        userMap.get(userId).notRead = false;
      }
    }
  });
  return Array.from(userMap.values()); // Convert map values to array
});

const getMessages = (userMessage) => {
  messageClicked.value = true;
  clickedMessage.value = userMessage; // so that can know which user is clicked
  const userId = userMessage.user.user_id;
  receiver.value = userMessage.user;
  messagesShowed.value = messages.value.filter(message => message.sender.user_id === userId || message.receiver.user_id === userId);

  if (messagesShowed.value.length < 1) {
    newMessage.value = 'Hi, I am ' + store.user.username + '. Let\'s start chatting';
  }

  // Send request to mark messages as read
  axios.post('/api/chat/mark-messages-as-read', {
    sender_id: store.user.user_id,
    receiver_id: userId
  }).then(response => {
    fetchMessages();

  }).catch(error => {
    console.error('Error marking messages as read', error);
  });


  setTimeout(() => {
    const chat = document.querySelector('.message');
    if(chat!==null){
      chat.scrollTop = chat.scrollHeight;
    }
  }, 100);
};


//send message
const sendMessage = async () => {
  if(images.value.length==0 && newMessage.value==''){
    errorMessages.value = 'Please enter a message or upload an image';
    isError.value=true;
    return;
  }
  showEmoji.value = false;
  await axios.post('/api/chat/messages', { message: newMessage.value, receiver_id: receiver.value.user_id, images: images.value});
  newMessage.value = '';
  images.value = [];
  showFile.value = false;
  
  // Immediately fetch messages after sending a new message
  fetchMessages();
  
  setTimeout(() => {
    const chat = document.querySelector('.message');
    if(chat!==null){
      chat.scrollTop = chat.scrollHeight;
    }
  }, 100);
};


//send message on enter
const sendMessageOnEnter = () => {
    if (newMessage.value.trim() !== '') { // Check if message is not empty
      sendMessage(); // Call sendMessage method
    }
  };

//fetch more messages
const fetchMoreMessage = () => {
  messagePerPage.value += 10;

  fetchMessages();
};

//image part:
const handleDrop = (event) => {
  event.preventDefault();
  const files = event.dataTransfer.files;
  handleFiles(files);
};

const handleFiles = (files) => {
  for (let i = 0; i < files.length; i++) {
    const file = files[i];
    if (file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = (e) => {
        images.value.push({ url: e.target.result, file });
      };
      reader.readAsDataURL(file);
    }
  }
};

const fileInput = ref(null);

const openFileInput = () => {
  if (fileInput.value) {
    fileInput.value.click();
  }
};

const handleFileInputChange = (event) => {
  const files = event.target.files;
  handleFiles(files);
};

const removeImage = (index) => {
  images.value.splice(index, 1);
};

// Google Map part
const showMapDialog = ref(false);
const map = ref(null);
const googleMap = ref(null);
const initialLocation = { lat: 1.4639, lng: 110.4283 }; // Coordinates for UNIMAS, Kota Samarahan
const searchQuery = ref('');
const selectedLocation = ref(null); // To store the selected location
const previousMarker = ref(null); // To store the previous red marker
const currentMarker = ref(null); // To store the current green marker
const infoWindow = ref(null); // To store the info window

const initializeMap = () => {
  if (map.value) {
    const google = window.google;
    const mapOptions = {
      center: initialLocation,
      zoom: 12,
    };
    googleMap.value = new google.maps.Map(map.value, mapOptions); // Update googleMap reference
    previousMarker.value = new google.maps.Marker({
      position: initialLocation,
      map: googleMap.value,
      icon: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png' // Set marker color to red
    });

    infoWindow.value = new google.maps.InfoWindow();

    // Add click event listener to the map
    googleMap.value.addListener('click', (event) => {
      const location = event.latLng;
      selectedLocation.value = location;
      googleMap.value.setCenter(location);

      // Remove the current marker if it exists
      if (currentMarker.value) {
        currentMarker.value.setMap(null);
      }

      // Set the previous marker to red
      if (previousMarker.value) {
        previousMarker.value.setIcon('http://maps.google.com/mapfiles/ms/icons/red-dot.png');
      }

      // Add the new green marker
      currentMarker.value = new google.maps.Marker({
        position: location,
        map: googleMap.value,
        icon: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png' // Set marker color to green
      });

      // Update the previous marker to be the current marker
      previousMarker.value = currentMarker.value;

      // Reverse geocode to get the location name
      const geocoder = new google.maps.Geocoder();
      geocoder.geocode({ location: location }, (results, status) => {
        if (status === 'OK' && results[0]) {
          const locationName = results[0].formatted_address;

          // Add click event listener to the marker
          google.maps.event.addListener(currentMarker.value, 'click', () => {
            // Get the user's current location
            navigator.geolocation.getCurrentPosition((position) => {
              const userLat = position.coords.latitude;
              const userLng = position.coords.longitude;

              const contentString = `
              <div style="text-align: left; font-family: Arial, sans-serif;">
                <p style="margin-bottom: 10px;"><strong>Selected Location:</strong> ${locationName}</p>
                <a href="https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${location.lat()},${location.lng()}" target="_blank" style="display: inline-block; margin: 5px; text-decoration: none; color: #4285F4;">
                  <img src="https://static-00.iconduck.com/assets.00/google-maps-icon-2048x2048-fxw1yxmx.png" alt="Google Maps" style="vertical-align: middle; width: 30px; height: 30px; margin-right: 3px;"/>
                  <span style="vertical-align: middle;">Open in Google Maps</span>
                </a>
                <br>
                <a href="https://waze.com/ul?ll=${location.lat()},${location.lng()}&navigate=yes&from=${userLat},${userLng}" target="_blank" style="display: inline-block; margin: 5px; text-decoration: none; color: #2CB1F2;">
                  <img src="https://i.pinimg.com/736x/e1/39/20/e139200f3e67ec44b6fa6a02d35d105d.jpg" alt="Waze" style="vertical-align: middle; width: 30px; height: 30px; margin-right: 3px;"/>
                  <span style="vertical-align: middle;">Open in Waze</span>
                </a>
                <br>
                <button id="copyLocationButton" style="display: inline-block; margin: 5px; padding: 5px 10px; font-size: 14px; cursor: pointer; background-color: #4CAF50; color: white; border: none; border-radius: 4px;">
                  Copy Location
                </button>
                <button id="sendLocationButton" style="display: inline-block; margin: 5px; padding: 5px 10px; font-size: 14px; cursor: pointer; background-color: #4CAF50; color: white; border: none; border-radius: 4px;">
                  Send Location
                </button>
              </div>
              `;

              infoWindow.value.setContent(contentString);
              infoWindow.value.open(googleMap.value, currentMarker.value);

              // Add event listener to the copy button
              setTimeout(() => {
                document.getElementById('copyLocationButton').addEventListener('click', () => {
                  const locationLink = `https://www.google.com/maps?q=${location.lat()},${location.lng()}`;
                  navigator.clipboard.writeText(locationLink).then(() => {
                    successMessages.value = 'Location link copied to clipboard!';
                    isSuccess.value = true;
                  }).catch((err) => {
                    console.error('Failed to copy text: ', err);
                  });
                });
              }, 0);

              // Add event listener to the send button, which is to send the location to the receiver, i want the link that can be click to open the location in google map
              setTimeout(() => {
                document.getElementById('sendLocationButton').addEventListener('click', () => {
                  newMessage.value = 'Location: ' + locationName + ' https://www.google.com/maps?q=' + location.lat() + ',' + location.lng();
                  sendMessage();
                  searchQuery.value = '';
                  showMapDialog.value = false;
                });
              }, 0);
            });
          });
        } else {
          console.error('Geocode was not successful for the following reason: ' + status);
        }
      });
    });
  }
};

const searchPlace = () => {
  const geocoder = new google.maps.Geocoder();
  geocoder.geocode({ address: searchQuery.value }, (results, status) => {
    if (status === 'OK') {
      successMessages.value = 'Location found!';
      isSuccess.value = true;
      const location = results[0].geometry.location;
      googleMap.value.setCenter(location); // Ensure googleMap is referenced correctly

      // Remove the current marker if it exists
      if (currentMarker.value) {
        currentMarker.value.setMap(null);
      }

      // Set the previous marker to red
      if (previousMarker.value) {
        previousMarker.value.setIcon('http://maps.google.com/mapfiles/ms/icons/red-dot.png');
      }

      // Add the new green marker
      currentMarker.value = new google.maps.Marker({
        position: location,
        map: googleMap.value,
        icon: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png' // Set marker color to green
      });

      // Update the previous marker to be the current marker
      previousMarker.value = currentMarker.value;

      // Reverse geocode to get the location name
      geocoder.geocode({ location: location }, (results, status) => {
        if (status === 'OK' && results[0]) {
          const locationName = results[0].formatted_address;

          // Add click event listener to the marker
          google.maps.event.addListener(currentMarker.value, 'click', () => {
            // Get the user's current location
            navigator.geolocation.getCurrentPosition((position) => {
              const userLat = position.coords.latitude;
              const userLng = position.coords.longitude;

              const contentString = `
              <div style="text-align: left; font-family: Arial, sans-serif;">
                <p style="margin-bottom: 10px;"><strong>Selected Location:</strong> ${locationName}</p>
                <a href="https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${location.lat()},${location.lng()}" target="_blank" style="display: inline-block; margin: 5px; text-decoration: none; color: #4285F4;">
                  <img src="https://static-00.iconduck.com/assets.00/google-maps-icon-2048x2048-fxw1yxmx.png" alt="Google Maps" style="vertical-align: middle; width: 30px; height: 30px; margin-right: 3px;"/>
                  <span style="vertical-align: middle;">Open in Google Maps</span>
                </a>
                <br>
                <a href="https://waze.com/ul?ll=${location.lat()},${location.lng()}&navigate=yes&from=${userLat},${userLng}" target="_blank" style="display: inline-block; margin: 5px; text-decoration: none; color: #2CB1F2;">
                  <img src="https://i.pinimg.com/736x/e1/39/20/e139200f3e67ec44b6fa6a02d35d105d.jpg" alt="Waze" style="vertical-align: middle; width: 30px; height: 30px; margin-right: 3px;"/>
                  <span style="vertical-align: middle;">Open in Waze</span>
                </a>
                <br>
                <button id="copyLocationButton" style="display: inline-block; margin: 5px; padding: 5px 10px; font-size: 14px; cursor: pointer; background-color: #4CAF50; color: white; border: none; border-radius: 4px;">
                  Copy Location
                </button>
                <button id="sendLocationButton" style="display: inline-block; margin: 5px; padding: 5px 10px; font-size: 14px; cursor: pointer; background-color: #4CAF50; color: white; border: none; border-radius: 4px;">
                  Send Location
                </button>
              </div>
              `;

              infoWindow.value.setContent(contentString);
              infoWindow.value.open(googleMap.value, currentMarker.value);

              // Add event listener to the copy button
              setTimeout(() => {document.getElementById('copyLocationButton').addEventListener('click', () => {
                  const locationLink = `https://www.google.com/maps?q=${location.lat()},${location.lng()}`;
                  navigator.clipboard.writeText(locationLink).then(() => {
                    successMessages.value = 'Location link copied to clipboard!';
                    isSuccess.value = true;
                  }).catch((err) => {
                    console.error('Failed to copy text: ', err);
                  });
                });
              }, 0);

              // Add event listener to the send button, which is to send the location to the receiver
              setTimeout(() => {
                document.getElementById('sendLocationButton').addEventListener('click', () => {
                  newMessage.value = 'Location: <br>' + locationName + ' https://www.google.com/maps?q=' + location.lat() + ',' + location.lng();
                  sendMessage();
                  searchQuery.value = '';
                  showMapDialog.value = false;
                });
              }, 0);


            });
          });
        } else {
          errorMessages.value = 'Location not found! Please enter more specific location details for better results.';
          isError.value = true;
          console.error('Geocode was not successful for the following reason: ' + status);
        }
      });
    } else {
      errorMessages.value = 'Location not found! Please enter more specific location details for better results.';
      isError.value = true;
      console.error('Geocode was not successful for the following reason: ' + status);
    }
  });
};

watch(showMapDialog, (newVal) => {
  if (newVal) {
    setTimeout(() => {
      initializeMap();
    }, 300); // Delay to ensure the dialog is fully rendered
  }
});




//split message
const splitMessage = (message) => {
  const words = message.split(' ');
  let lines = [];
  let currentLine = '';

  words.forEach(word => {
    if ((currentLine + word).length <= 40) {
      currentLine += word + ' ';
    } else {
      lines.push(currentLine.trim());
      currentLine = word + ' ';
    }
  });

  if (currentLine.length > 0) {
    lines.push(currentLine.trim());
  }

  return makeLinksClickable(lines.join('<br>'));
};

const makeLinksClickable = (message) => {
  const urlPattern = /https?:\/\/[^\s]+/g;
  return message.replace(urlPattern, function(url) {
    return `<a href="${url}" target="_blank">${url}</a>`;
  });
};




// Initialize Pusher
var pusher = new Pusher('bfdcd4030f09a5a101b7', {
  cluster: 'ap1'
});

// Subscribe to the channel
var channel = pusher.subscribe('my-channel');

// Check if notifications are supported
if (window.Notification) {
  // Request permission to show notifications
  Notification.requestPermission().then(function(permission) {
    if (permission === 'granted') {
      // Bind to the event
      channel.bind('MessageSent', function(data) {
        // console.log('MessageSent event triggered', data); 

        fetchMessages(); // Fetch the updated messages

        // Update the messages array
        app.messages.push(data);

        // Check if the received message is for the current user
        if (data.message.receiver_id == currentUserID.value) {
          hasNewMessage.value = true;

          // Show an alert if the user is not on the chat page
          if (route.name !== 'Chat') {
            new Notification('New Message', {
              body: 'You have a new message, please check your chat box',
              icon: 'https://cdn3d.iconscout.com/3d/premium/thumb/message-4737170-3934375.png'
            });
          }
        }
      });
    } else {
      // console.log('Notification permission denied.');
    }
  });
} else {
  alert('Notifications aren\'t supported on your browser! :(');
}

// Your app structure
const app = {
  messages: []
};


//watch for changes in the search input
watch(searchUser, debounce(() => {
  
  if(searchUser.value.length > 2) {
    page = 1;
    //clear latestUserMessages
    messages.value = [];
    latestUserMessages.value = [];

    fetchMessages();
  }else{
    fetchMessages();
    return;
  }
  
}, 800));

fetchMessages();
getUser();
getFollowingUsers();
</script>

<template>
  <VContainer>

    <!-- Chat box in chat page-->
    <h1 v-if="props.newChatUser==null">Messages</h1>
    <VCard v-if="props.newChatUser==null" class="chat-box">
    <VRow no-gutters>
      <VCol cols="12" md="3">
        <VCard class="user-list">
          <div class="d-flex mt-3 mb-3 ml-3" >
            <VRow>
              <VCol cols="12" md="2">
                <VAvatar v-if="user.avatar" size="40">
                  <VImg :src="user.avatar" />
                  
                </VAvatar>
              </VCol>
              <VCol cols="12" md="10">
                <VTextField
                  v-model="searchUser"
                  class="mr-3"
                  label="Search User"
                  prepend-inner-icon="ri-search-line"
                  placeholder="Search User"
                  rounded
                  density="compact"

                />
              </VCol>
            </VRow>
          </div>
          <VDivider />
          <VCardTitle>
            <VRow align="center" justify="space-between">
              <VCol class="d-flex align-start">
                <span style="font-size: 20px; font-weight: 100;">Chats</span>
              </VCol>
              <VCol class="d-flex justify-end">
                <VTooltip
                  open-delay="500"
                  location="right"
                  activator="parent"
                  transition="scroll-x-transition"
                >
                  <span>Click to have a chat with your friends</span>
                </VTooltip>
                <VBtn
                  icon="ri-chat-new-line"
                  variant="text"
                  @click="openFollowedUsersDialog = true"
                />
              </VCol>
            </VRow>
          </VCardTitle>
          <VCardText>
            <!-- Display latest message for each user -->
            <VList density="compact">
              <VListItem 
                v-for="userMessage in latestUserMessages" 
                :key="userMessage.user_id" 
                @click="getMessages(userMessage)"
                class="font-weight-medium text-high-emphasis text-truncate"
        
              >
                <div class="d-flex align-items-center justify-content-between">
                  <div class="d-flex align-items-center">
                
                    <VAvatar v-if="userMessage.user.avatar" size="35">
                      <VImg :src="userMessage.user.avatar" />
                    </VAvatar>
                    <div class="ml-2">
                      <div style="font-size: 13px;">{{ userMessage.user.username }}</div>
                      <div 
                        v-if="userMessage.message !== null && userMessage.isLocation == false" 
                        style="color: #6c757d; font-size: 12px;"
                      >
                        {{ userMessage.message }}
                      </div>
                      <div 
                        v-else-if="userMessage.isPicture && userMessage.message == null" 
                        style="color: #6c757d; font-size: 12px;"
                      >
                        <VIcon icon="ri-camera-3-line" color="secondary" /> Photo
                      </div>
                      <div 
                        v-else-if="userMessage.message !== null & userMessage.isLocation" 
                        style="color: #6c757d; font-size: 12px;"
                      >
                        <VIcon icon="ri-map-pin-line" color="success" /> Location
                      </div>
                      <div 
                        v-else-if="!userMessage.isPicture && userMessage.message == null" 
                        style="color: #6c757d; font-size: 12px;"
                      >
                        No message yet
                      </div>
                    </div>
                  </div>
                  <VIcon v-if="userMessage.notRead==true" icon="ri-circle-fill" size="12px" class="ml-1" color="error" />
                </div>
                <hr style="position: absolute; border-block-start: 0 solid; inset-block-end: 0; inset-inline: 0 0;">
              </VListItem>
            </VList>

          </VCardText>

        </VCard>
        </VCol>

        <!-- Chat box -->
        <VCol v-if="messageClicked===true" cols="12" md="9" >

        <VCard class="user-list">
          <!-- Receiver information -->

          <div class="d-flex align-items-center mt-3 mb-3 ml-3" >
            <VAvatar v-if="receiver.avatar" size="40">
              <VImg :src="receiver.avatar" />
            </VAvatar>
            <div class="ml-2">
              <div>{{ receiver.username }}</div>
              <div style="color: #6c757d; font-size: 12px;">{{ receiver.email }}</div>
            </div>
          </div>

          <VDivider />
          <VCardText>
            <!-- Display messages -->
            
            <div v-if="messagesShowed.length > 0" class="message">
              <!--button to load more message-->
              <div class="text-center">
                <VBtn v-if="messagesShowed.length > 9" variant="text" @click="fetchMoreMessage" color="primary">
                  <VIcon class="mr-1" icon="ri-arrow-up-circle-line" /> Load more messages
                </VBtn>
              </div>

              <div v-for="(message, index) in messagesShowed" :key="message.id" :class="{'right-message': message.sender.user_id === store.user.user_id, 'left-message': message.sender.user_id !== store.user.user_id}">
                <div v-if="message.sender.user_id !== store.user.user_id" class="message-container left-message">
                  <img v-if="showAvatar(index)" :src="message.sender.avatar" alt="Avatar" class="avatar">
                  <span >
                    <VChip v-if="showAvatar(index)" label class="mt-1 ml-2 mb-1" dense :style="{ height: 'auto', 'min-height': '32px', 'line-height': '16px' }">
                      <div style="display: inline-block; text-align: start;">
                          <div v-if="message.pictures && message.pictures.length">
                            <div class="mt-2"></div>
                            <VImg v-for="picture in message.pictures" :key="picture.picture_id" :src="picture.picture_path" alt="Message Image" class="mb-2" :class="{'right-message-picture': message.sender.user_id === store.user.user_id, 'left-message-picture': message.sender.user_id !== store.user.user_id}" style=" block-size: auto;inline-size: 200px;" />
                            <hr class="mt-1 mb-1" style="border-block-start: 0 solid; inset-block-end: 0; inset-inline: 0 0;"/>
                          </div>
                          <div v-if="message.message !== null">
                            <template v-if="message.message.length > 40">
                              <span v-html="splitMessage(message.message)"></span>
                            </template>
                            <template v-else>
                              <span v-html="splitMessage(message.message)"></span>
                            </template>
                          </div>
                      </div>
                    </VChip>
                    <VChip v-else label class="mt-1 ml-11 mb-1" dense :style="{ height: 'auto', 'min-height': '32px', 'line-height': '16px' }">
                      <div style="display: inline-block; text-align: start;">
                        <div v-if="message.pictures && message.pictures.length" >
                          <div class="mt-2"></div>
                            <VImg v-for="picture in message.pictures" :key="picture.picture_id" :src="picture.picture_path" alt="Message Image" class="mb-2" :class="{'right-message-picture': message.sender.user_id === store.user.user_id, 'left-message-picture': message.sender.user_id !== store.user.user_id}" style=" block-size: auto;inline-size: 200px;" />
                            <hr class="mt-1 mb-1" style="border-block-start: 0 solid; inset-block-end: 0; inset-inline: 0 0;"/>
                        </div>
                        <div v-if="message.message !== null">
                          <template v-if="message.message.length > 40">
                            <span v-html="splitMessage(message.message)"></span>
                          </template>
                          <template v-else>
                            <span v-html="splitMessage(message.message)"></span>
                          </template>
                        </div>


                    </div>
                    </VChip>
                  </span>
                </div>
                <div v-else class="message-container right-message">
                  <span>
                    <VChip v-if="showAvatar(index)" label class="mt-1 mr-2 mb-1" dense :style="{ height: 'auto', 'min-height': '32px', 'line-height': '16px' }">
                      <div style="flex-wrap: wrap;" >
                        <div v-if="message.pictures && message.pictures.length">
                          <div class="mt-2"></div>
                          <VImg v-for="picture in message.pictures" :key="picture.picture_id" :src="picture.picture_path" alt="Message Image" class="mb-2" :class="{'right-message-picture': message.sender.user_id === store.user.user_id, 'left-message-picture': message.sender.user_id !== store.user.user_id}" style=" block-size: auto;inline-size: 200px;" />
                          <hr class="mt-1 mb-1" style="border-block-start: 0 solid; inset-block-end: 0; inset-inline: 0 0;"/>
                        </div>
                        <div v-if="message.message !== null">
                          <template v-if="message.message.length > 40">
                            <span v-html="splitMessage(message.message)"></span>
                          </template>
                          <template v-else>
                            <span v-html="splitMessage(message.message)"></span>
                          </template>
                        </div>


                      </div>
                    </VChip>
                    <VChip v-else label class="mt-1 mr-11 mb-1" dense :style="{ height: 'auto', 'min-height': '32px', 'line-height': '16px' }">
                      <div style="flex-wrap: wrap;" >
                        <div v-if="message.pictures && message.pictures.length">
                          <div class="mt-2"></div>
                          <VImg v-for="picture in message.pictures" :key="picture.picture_id" :src="picture.picture_path" alt="Message Image" class="mb-2" :class="{'right-message-picture': message.sender.user_id === store.user.user_id, 'left-message-picture': message.sender.user_id !== store.user.user_id}" style=" block-size: auto;inline-size: 200px;" />
                          <hr class="mt-1 mb-1" style="border-block-start: 0 solid; inset-block-end: 0; inset-inline: 0 0;"/>
                        </div>
                        <div v-if="message.message !== null">
                          <template v-if="message.message.length > 40">
                            <span v-html="splitMessage(message.message)"></span>
                          </template>
                          <template v-else>
                            <span v-html="splitMessage(message.message)"></span>
                          </template>
                        </div>
                      </div>
                    </VChip>
                  </span>
                  <img v-if="showAvatar(index)" :src="message.sender.avatar" alt="Avatar" class="avatar">
      
                </div>
              </div>
            </div>

            <div v-else class="text-center" style=" margin-block:40px 40px">
              <VCardTitle>Messages</VCardTitle>
              <VImg :src="chatBackground" style=" block-size: auto;inline-size: 100px; margin-block-start: 20px;margin-inline-start:220px"/>
              <VCardTitle class="text-overline">You guys are friends on UNIMAS Web Application</VCardTitle>
              <VCardText>
                <VList>
                  <VListItem>
                    <VListItemTitle>Let's start chatting</VListItemTitle>
                  </VListItem>
                </VList>
              </VCardText>
            </div>
            
            <!-- Message input -->
            <VRow class="mt-4 mb-0 ml-2 mr-2" style="position: absolute; inset-block-end: 0; inset-inline: 0 0;">
              <emoji-picker
                  v-if="showEmoji===true" 
                  @emoji-click="addEmoji" 
                  theme="light"
                  :native="true"
                  class="light ml-3"
   
        
                ></emoji-picker>

                <VCard v-if="showFile">
                  <div class="upload-container ml-2 mt-2 mr-2">
                    <input
                      type="file"
                      ref="fileInput"
                      style="display: none"
                      accept="image/*"
                      @change="handleFileInputChange"
                    />
                    <div
                      class="upload-box"
                      @drop.prevent="handleDrop"
                      @dragover.prevent
                      @click="openFileInput"
                    >
                      <VIcon class="upload-icon">ri-image-add-line</VIcon>
                      <p class="upload-text">Drag & Drop your images here.</p>
                      <p class="upload-text">Click here to upload images (PNG, JPEG, JPG).</p>
                      <div v-if="images.length > 0" class="uploaded-images">
                        <p class="upload-text">Selected images: </p>
                      <div v-for="(image, index) in images" :key="index" class="uploaded-image">
                        <img :src="image.url" alt="Uploaded Image" />
                        <VBtn @click="removeImage(index)" icon="ri-close-line" class="remove-btn"/>
                      </div>
                    </div>
                    </div>
                  </div>
                </VCard>
               
              <VCol cols="12">
                <VTextField 
                  v-model="newMessage" 
                  label="Type your message here" 
                  @keyup.enter="sendMessageOnEnter" 
                  :append-inner-icon="showEmoji ? 'ri-emotion-happy-line' : 'ri-emotion-happy-line'"
                  @click:append-inner="(showEmoji = !showEmoji, showFile = false, showMapDialog = false)"
                >
                  <template #append>
                    <VBtn @click="(showMapDialog = true,showEmoji =false,showFile =false)" variant="text" color="success"><VIcon icon="ri-map-pin-line"/></VBtn>
                    <VBtn @click="(showFile = !showFile, showEmoji=false, showMapDialog=false)" variant="text" color="error"><VIcon :icon="showFile ? 'ri-close-line' : 'ri-attachment-line'"/></VBtn>
                    <VBtn @click="sendMessage"><VIcon icon="ri-send-plane-fill"/></VBtn>
                  </template>
                  
                </VTextField>
                
                
              </VCol>
              
            </VRow>
            
          </VCardText>
          
        </VCard>
        
      </VCol>
      <VCol v-else cols="12" md="9" >
        <VRow>
          <VCol cols="12" md="3">
            <!--Add a image here (avatar-11) by using the import on script setup-->
            <VImg :src="chatBackground" style=" block-size: auto;inline-size: 600px; margin-block-start: 160px; margin-inline-start: 50px;"/>
          </VCol>
          <VCol cols="12" md="9">
            <div class="text-center" style=" margin-block:180px 180px">
          <VCardTitle><VIcon icon="ri-emotion-happy-line" color="success"/> Chit Chat with Friends</VCardTitle>
          <VCardTitle class="text-overline">Try to make friends on UNIMAS Web Application</VCardTitle>
          <VCardText>
            <VList>
              <VListItem>
                <VListItemTitle class="text-overline">If you haven't followed anyone,</VListItemTitle>
                <VListItemTitle class="text-overline"><b> you can follow someone in the Life Moment Post page</b></VListItemTitle>
              
              </VListItem>
            </VList>
          </VCardText>
          </div>
          </VCol>
        </VRow>
      </VCol>
    </VRow>
  </VCard>




  <!-- Chat box in life moment post-->
  <VCard v-else>
    <VCard class="user-list">
      <!-- Receiver information -->

      <div class="d-flex align-items-center mt-3 mb-3 ml-3" >
        <VAvatar v-if="props.newChatUser.avatar" size="50">
          <VImg :src="props.newChatUser.avatar" />
        </VAvatar>
        <div class="ml-2">
          <div>{{ props.newChatUser.username }}</div>
          <div style="color: #6c757d; font-size: 12px;">{{ props.newChatUser.email }}</div>
        </div>
      </div>

      <VDivider />
      <VCardText>
        <!-- Display messages -->
        
        <div v-if="messagesShowed.length>0" class="message">
          <!--button to load more message-->
          <div class="text-center">
            <VBtn v-if="messagesShowed.length>9" variant="text" @click="fetchMoreMessage" color="primary"><VIcon class="mr-1" icon="ri-arrow-up-circle-line"/> Load more messages</VBtn>
          </div>
          
          <div v-for="(message, index) in messagesShowed" :key="message.id" :class="{'right-message': message.sender.user_id === store.user.user_id, 'left-message': message.sender.user_id !== store.user.user_id}">
            <div v-if="message.sender.user_id !== store.user.user_id" class="message-container left-message">
              <img v-if="showAvatar(index)" :src="message.sender.avatar" alt="Avatar" class="avatar">
              <span>
                <VChip v-if="showAvatar(index)" label class="mt-1 ml-2 mb-1" dense :style="{ height: 'auto', 'min-height': '32px', 'line-height': '16px' }">
                  <div style="display: inline-block; text-align: start;">
                      <div v-if="message.pictures && message.pictures.length">
                        <div class="mt-2"></div>
                        <VImg v-for="picture in message.pictures" :key="picture.picture_id" :src="picture.picture_path" alt="Message Image" class="mb-2" :class="{'right-message-picture': message.sender.user_id === store.user.user_id, 'left-message-picture': message.sender.user_id !== store.user.user_id}" style=" block-size: auto;inline-size: 200px;" />
                        <hr class="mt-1 mb-1" style="border-block-start: 0 solid; inset-block-end: 0; inset-inline: 0 0;"/>
                      </div>
                      <div v-if="message.message !== null">
                        <template v-if="message.message.length > 40">
                          <span v-html="splitMessage(message.message)"></span>
                        </template>
                        <template v-else>
                          <span v-html="splitMessage(message.message)"></span>
                        </template>
                      </div>

                  </div>
                </VChip>
                <VChip v-else label class="mt-1 ml-11 mb-1" dense :style="{ height: 'auto', 'min-height': '32px', 'line-height': '16px' }">
                  <div style="display: inline-block; text-align: start;">
                    <div v-if="message.pictures && message.pictures.length" >
                      <div class="mt-2"></div>
                        <VImg v-for="picture in message.pictures" :key="picture.picture_id" :src="picture.picture_path" alt="Message Image" class="mb-2" :class="{'right-message-picture': message.sender.user_id === store.user.user_id, 'left-message-picture': message.sender.user_id !== store.user.user_id}" style=" block-size: auto;inline-size: 200px;" />
                        <hr class="mt-1 mb-1" style="border-block-start: 0 solid; inset-block-end: 0; inset-inline: 0 0;"/>
                    </div>
                    <div v-if="message.message !== null">
                      <template v-if="message.message.length > 40">
                        <span v-html="splitMessage(message.message)"></span>
                      </template>
                      <template v-else>
                        <span v-html="splitMessage(message.message)"></span>
                      </template>
                    </div>

                </div>
                </VChip>
              </span>
            </div>
            <div v-else class="message-container right-message">
              <span>
                <VChip v-if="showAvatar(index)" label class="mt-1 mr-2 mb-1" dense :style="{ height: 'auto', 'min-height': '32px', 'line-height': '16px' }">
                      <div style="flex-wrap: wrap;" >
                        <div v-if="message.pictures && message.pictures.length">
                          <div class="mt-2"></div>
                          <VImg v-for="picture in message.pictures" :key="picture.picture_id" :src="picture.picture_path" alt="Message Image" class="mb-2" :class="{'right-message-picture': message.sender.user_id === store.user.user_id, 'left-message-picture': message.sender.user_id !== store.user.user_id}" style=" block-size: auto;inline-size: 200px;" />
                          <hr class="mt-1 mb-1" style="border-block-start: 0 solid; inset-block-end: 0; inset-inline: 0 0;"/>
                        </div>
                        <div v-if="message.message !== null">
                          <template v-if="message.message.length > 40">
                            <span v-html="splitMessage(message.message)"></span>
                          </template>
                          <template v-else>
                            <span v-html="splitMessage(message.message)"></span>
                          </template>
                        </div>

                      </div>
                    </VChip>
                    <VChip v-else label class="mt-1 mr-11 mb-1" dense :style="{ height: 'auto', 'min-height': '32px', 'line-height': '16px' }">
                      <div style="flex-wrap: wrap;" >
                        <div v-if="message.pictures && message.pictures.length">
                          <div class="mt-2"></div>
                          <VImg v-for="picture in message.pictures" :key="picture.picture_id" :src="picture.picture_path" alt="Message Image" class="mb-2" :class="{'right-message-picture': message.sender.user_id === store.user.user_id, 'left-message-picture': message.sender.user_id !== store.user.user_id}" style=" block-size: auto;inline-size: 200px;" />
                          <hr class="mt-1 mb-1" style="border-block-start: 0 solid; inset-block-end: 0; inset-inline: 0 0;"/>
                        </div>
                        <div v-if="message.message !== null">
                          <template v-if="message.message.length > 40">
                            <span v-html="splitMessage(message.message)"></span>
                          </template>
                          <template v-else>
                            <span v-html="splitMessage(message.message)"></span>
                          </template>
                        </div>

                      </div>
                    </VChip>
              </span>
              <img v-if="showAvatar(index)" :src="message.sender.avatar" alt="Avatar" class="avatar">
            </div>
          </div>
        </div>

        <div v-else class="text-center" style=" margin-block:40px 40px">
          <VCardTitle>Messages</VCardTitle>
          <VImg :src="chatBackground" style=" block-size: auto;inline-size: 100px; margin-block-start: 20px;margin-inline-start:370px"/>
          <VCardTitle class="text-overline">You guys are friends on UNIMAS Web Application</VCardTitle>
          <VCardText>
            <VList>
              <VListItem>
                <VListItemTitle>Let's start chatting</VListItemTitle>
              </VListItem>
            </VList>
          </VCardText>
        </div>
        
         <!-- Message input -->
         <VRow class="mt-4 mb-0 ml-2 mr-2" style="position: absolute; inset-block-end: 0; inset-inline: 0 0;">
              <emoji-picker
                  v-if="showEmoji===true" 
                  @emoji-click="addEmoji" 
                  theme="light"
                  :native="true"
                  class="light ml-3"
   
        
                ></emoji-picker>

                <VCard v-if="showFile">
                  <div class="upload-container ml-2 mt-2 mr-2">
                    <input
                      type="file"
                      ref="fileInput"
                      style="display: none"
                      accept="image/*"
                      @change="handleFileInputChange"
                    />
                    <div
                      class="upload-box"
                      @drop.prevent="handleDrop"
                      @dragover.prevent
                      @click="openFileInput"
                    >
                      <VIcon class="upload-icon">ri-image-add-line</VIcon>
                      <p class="upload-text">Drag & Drop your images here.</p>
                      <p class="upload-text">Click here to upload images (PNG, JPEG, JPG).</p>
                      <div v-if="images.length > 0" class="uploaded-images">
                        <p class="upload-text">Selected images: </p>
                      <div v-for="(image, index) in images" :key="index" class="uploaded-image">
                        <img :src="image.url" alt="Uploaded Image" />
                        <VBtn @click="removeImage(index)" icon="ri-close-line" class="remove-btn"/>
                      </div>
                    </div>
                    </div>
                  </div>
                </VCard>
               
              <VCol cols="12">
         
                <VTextField 
                  v-model="newMessage" 
                  label="Type your message here" 
                  @keyup.enter="sendMessageOnEnter" 
                  :append-inner-icon="showEmoji ? 'ri-emotion-happy-line' : 'ri-emotion-happy-line'"
                  @click:append-inner="(showEmoji = !showEmoji, showFile = false, showMapDialog = false)"
                >
                  <template #append>
                    <VBtn @click="(showMapDialog = true,showEmoji =false,showFile =false)" variant="text" color="success"><VIcon icon="ri-map-pin-line"/></VBtn>
                    <VBtn @click="(showFile = !showFile, showEmoji=false, showMapDialog=false)" variant="text" color="error"><VIcon :icon="showFile ? 'ri-close-line' : 'ri-attachment-line'"/></VBtn>
                    <VBtn @click="sendMessage"><VIcon icon="ri-send-plane-fill"/></VBtn>
                  </template>
                  
                </VTextField>
                
                
              </VCol>
              
            </VRow>
      </VCardText> 
    </VCard>
  </VCard>


  </VContainer>

  <VDialog
    v-model="openFollowedUsersDialog"
    width="400"
    scrollable
    style="max-block-size: 350px; overflow-y: auto;"
  >
    <VCard
      title="Followed Users"
    >
      <FollowingList
        :followingUsers="followingUsers"
      />
    </VCard>
  </VDialog>

  <VDialog v-model="showMapDialog" width="800">
  <VCard>
    <VCardTitle class="text-center text-overline mt-3 mb-1" style="font-size: 20px !important;">
      <span>Google Map</span>
      <VSpacer />
    </VCardTitle>
    <VAlert
      color="info"
      icon="ri-map-pin-line"
      variant="tonal"
      style=" margin-block-end: 20px;margin-inline: 20px 20px;"
    >
      Use the search bar to find a specific place. Click on the map to select a location.
    </VAlert>
   
    <VCardText>
      <div class="box-style" ref="map" style="block-size: 400px;"></div>
      
      <div class="mt-4">
        <VTextField
          v-model="searchQuery"
          label="Search Location"
          @keyup.enter="searchPlace"
          clearable
        > 
        <template #append>
          <VBtn @click="searchPlace"><VIcon icon="ri-search-line"/></VBtn>
        </template>
        </VTextField>
      </div>
      <!-- <div v-if="selectedLocation">
        <p>Selected Location: {{ selectedLocation.lat() }}, {{ selectedLocation.lng() }}</p>
      </div> -->
    </VCardText>
    
  </VCard>
</VDialog>


  <!-- Error Snackbar -->
  <VSnackbar
      v-model="isError"
      location="top end"
      transition="scale-transition"
      color="error"
    >
    <VIcon size="20" class="me-2">ri-error-warning-line</VIcon>
    <span>{{ errorMessages.value }}</span>
  </VSnackbar>

  <VSnackbar
      v-model="hasNewMessage"
      location="top end"
      transition="scale-transition"
      color="info"
    >
    <VIcon size="20" class="me-2">ri-chat-1-fill</VIcon>
    <span>There is a new message</span>
  </VSnackbar>

  <VSnackbar
      v-model="isSuccess"
      location="top end"
      transition="scale-transition"
      color="success"
    >
    <VIcon size="20" class="me-2">ri-checkbox-circle-line</VIcon>
    <span>{{ successMessages }}</span>
  </VSnackbar>
</template>


<style scoped>
.box-style {
  padding: 1.5px; /* Padding around the table */
  border: 0.4px solid #282828;
  border-radius:10px;
  background-color: #fff; /* White background color */
  box-shadow: 0 0 20px rgba(0, 0, 0, 30%); /* Drop shadow */
  margin-block-end: 15px
}

.chat-box {
  block-size: 550px; /* Set the desired height */
  inline-size: 950px; /* Set the width to 100% */
}

.user-list {
  block-size: 550px; /* Set the desired height */
  overflow-y: auto; /* Add a scrollbar */
}

.chat-list{
  block-size: 550px; /* Set the desired height */
}

.chat{
  block-size: 400px; /* Set the desired height */
  overflow-y: auto; /* Add a scrollbar */
}

.left-message {
  text-align: end;
}

.right-message {
  text-align: start;
}

.avatar {
  border-radius: 50%;
  block-size: 35px;
  inline-size: 35px;
}

.message{
  max-block-size: 390px; /* Set a maximum height for the message container */
  overflow-y: auto; /* Enable vertical scrolling */
  
}

.message-container {
  display: flex;
  flex-wrap: nowrap; /* Prevent messages from wrapping */
  margin-block-end: 5px; /* Add margin between messages */
  margin-inline: 0; /* Reset left and right margins */
}


.left-message .message-container {
  justify-content: flex-start;
}

.right-message .message-container {
  justify-content: flex-end;
}

/* Ensure that messages without avatars align properly */
.message-container.no-avatar {
  justify-content: flex-start; /* Align to the left for messages without avatars */
}


.upload-container {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.upload-box {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  border: 2px dashed #ccc;
  border-radius: 8px;
  block-size: 300px; /* Adjust the height as needed */
  cursor: pointer;
  inline-size: 400px; /* Adjust the width as needed */
  margin-block-end: 10px;
}

.upload-icon {
  font-size: 48px;
}

.upload-text {
  margin-block-start: 8px;
}

.uploaded-images {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-block-start: 20px;
}

.uploaded-image {
  position: relative;
}

.uploaded-image img {
  border-radius: 8px;
  max-block-size: 100px; /* Adjust the size as needed */
  max-inline-size: 100px; /* Adjust the size as needed */
}

.remove-btn {
  position: absolute;
  inset-block-start: 5px;
  inset-inline-end: 5px;
}

.button-container {
  display: flex;
  justify-content: space-between;
  gap: 440px;
}


.left-message-picture {
  border-radius: 8px;
  margin-block-end:20px;
  max-block-size: 200px; /* Adjust the size as needed */
  max-inline-size: 200px; /* Adjust the size as needed */
}

.right-message-picture {
  border-radius: 8px;
  max-block-size: 200px; /* Adjust the size as needed */
  max-inline-size: 200px; /* Adjust the size as needed */
}

.image-box {
  padding: 5px; /* Add some padding to give space around the images */
  border: 1px solid #ccc; /* You can adjust the border style and color as needed */
  margin-block-end: 10px; /* Add margin at the bottom to separate from other content */
}


</style>
