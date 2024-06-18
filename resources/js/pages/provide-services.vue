<script setup>
import { requiredValidator } from '@/@core/utils/validators';
import TableService from '@/components/Service/ServiceTable.vue';
import { useAuthStore } from '@/plugins/store/AuthStore';
import axios from 'axios';
import { debounce } from 'lodash';
import { watch } from 'vue';

const store = useAuthStore();
const searchValue = ref('');
const rowPerPage = ref(5);
const currentPage = ref(1);
const totalPages = ref(10);
const services = ref([]);
const addNewDialog = ref(false);
const addPictureBox = ref(false);
const errorMessages = ref('');
const hasErrorAlert = ref(false);
const isAddAlert = ref(false);
const typeSearch = ref([]);
const sortService = ref(null);
const searchValueAllServices = ref('');
const ownServices = ref(false);
const sliderValues = ref([0, 80])



const name = ref('');
const description = ref('');
const type = ref(null);
const pricePerHour = ref('');
const pictures = ref([]);
const allServices = ref([]);
const rowPerPageAllServices = ref(8);
const currentPageAllServices = ref(1);
const totalPagesAllServices = ref(0);
const user = ref([]);  

const sortBy = [
  { title: 'Price:Low to High', value: 'Price:Low to High' }, 
  { title: 'Price:High to Low', value: 'Price:High to Low' },
  { title:'Service name: A to Z', value: 'name: A to Z'},
  { title:'Service name: Z to A', value: 'name: Z to A'},
];

const types = [
  'Web Development',
  'Mobile App Development',
  'Graphic Design',
  'Digital Marketing',
  'SEO Optimization',
  'Content Writing',
  'Video Production',
  'Social Media Management',
  'IT Support',
  'Consulting Services',
];

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
    // check if the user has a role name of Seller, if yes, assign isSeller to true
    user.value.roles.forEach((role) => {
      if(role.name === 'Seller'){
        user.value.isSeller = true;
      }
    });
  } catch (error) {

    //go to error page
    router.push('/error-unauthorized');

    console.error(error);
  }
};

getUser();


const serviceLoad = debounce(() => {
  let requestURL = '/api/services/get-auth-services?per_page=' + rowPerPage.value + '&page=' + currentPage.value;
  if (searchValue.value && searchValue.value.length > 2) {
    requestURL += '&search=' + searchValue.value;
  }

  axios.get(requestURL)
    .then(({data}) => {
      totalPages.value = Math.ceil(data.data.total / rowPerPage.value);
      services.value = data.data.data;
      services.value.forEach((service) => {
      service.pictures.forEach((picture) => {
        picture.picture_path = 'http://127.0.0.1:8000/storage/' + picture.picture_path;
      });
    });
      
    })
    .catch(error => {
      console.log(error)
    })
},800)

const allServicesLoad = debounce(() => {
  let requestURL='/api/services/get-all-services?per_page='+rowPerPageAllServices.value+'&page='+currentPageAllServices.value;
  if(typeSearch.value && typeSearch.value.length > 0){
    requestURL += '&type='+typeSearch.value;
  }
  if(searchValueAllServices.value && searchValueAllServices.value.length > 2){
    requestURL += '&search='+searchValueAllServices.value;
  }
  if(sortService.value){
    if(sortService.value === 'Price:Low to High'){
      requestURL += '&sort=price_per_hour&order=asc';
    }
    else if(sortService.value === 'Price:High to Low'){
      requestURL += '&sort=price_per_hour&order=desc';
    }
    else if(sortService.value === 'name: A to Z'){
      requestURL += '&sort=name&order=asc';
    }
    else if(sortService.value === 'name: Z to A'){
      requestURL += '&sort=name&order=desc';
    }
  }
  if(sliderValues.value[0] !== 0 || sliderValues.value[1] !== 80){
    requestURL += '&min_price='+sliderValues.value[0]+'&max_price='+sliderValues.value[1];
  }
  axios.get(requestURL).then(({data}) => {
    totalPagesAllServices.value = Math.ceil(data.data.total / rowPerPageAllServices.value);
    allServices.value = data.data.data;
    

    allServices.value.forEach((service) => {
      service.pictures.forEach((picture) => {
        picture.picture_path = 'http://127.0.0.1:8000/storage/' + picture.picture_path;
      });
    });
    
    // if the user_id is the same as the user logged in, then the save the service_id
    allServices.value.forEach((service) => {
      if(service.user_id === store.user.user_id){
        service.isOwn = true;
      }
      else{
        service.isOwn = false;
      }
    });
    
     // make the created_at date more readable, remove seconds, make it am/pm
     allServices.value.forEach((service) => {
      service.created_at = new Date(service.created_at).toLocaleString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: 'numeric',
        minute: 'numeric',
        hour12: true,
      });
      
    });
  }).catch((error) => {
    console.log(error);
  });
},800)


const createService = () => {
  axios.post('/api/services/store', {
    user_id: store.user.user_id,
    name: name.value,
    description: description.value,
    price_per_hour: pricePerHour.value,
    type: type.value,
    pictures: pictures.value,
    availability: 'Available',
  }).then((response) => {
    name.value = '';
    description.value = '';
    pricePerHour.value = '';
    type.value = null;

    addNewDialog.value = false;
    pictures.value = [];
    addPictureBox.value = false;
    isAddAlert.value = true;
    serviceLoad();
    
  }).catch((error) => {
    errorMessages.value = error.response.data.message;
    hasErrorAlert.value = true;
    
    console.log(error);
  });
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
        pictures.value.push({ url: e.target.result, file });
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
  pictures.value.splice(index, 1);
};
//image done


const priceValidator = (value) => {
  if (!value) return 'Price is required';
  if (isNaN(value)) return 'Price must be a number';
  return true;
};

watch([searchValue, rowPerPage, currentPage], () => {
  serviceLoad();
});

watch([rowPerPageAllServices, currentPageAllServices, typeSearch,  searchValueAllServices, sortService], () => {
  allServicesLoad();
});


watch(sliderValues, debounce((newValue) => {
  allServicesLoad();
}, 1500), { immediate: true, deep: true });


serviceLoad()
allServicesLoad()
</script>

<template>
  <div class="box-style">
    <h2 class="mt-3 ml-3 text-overline-4" style="font-weight: 400;">Filter Services</h2>
    <VRow class="mb-2 mt-2 ml-2 mr-2">
      <VCol cols="12" md="4">
        <VCombobox
          v-model="typeSearch"
          multiple
          chips
          :items="types"
          prepend-inner-icon="ri-price-tag-3-line"
          item-title="name"
          placeholder="Select type"
          return-object
          label="Type"
          clearable
        />

      </VCol>
      
      <VCol cols="12" md="4">
        <VRangeSlider
          v-model="sliderValues"
          :min="0"
          :max="500"
          thumb-label="always"
          step="10"
          color="primary"
          aria-label="Price range slider"
        >
          <template #prepend>
            <VIcon small color="primary">mdi-currency-usd</VIcon>
          </template>
          <template #append>
            <VIcon small color="primary">mdi-currency-usd</VIcon>
          </template>
        </VRangeSlider>
          <p class="text-center mt-2" @click="sliderValues = [0, 80]">
            <VTooltip
              open-delay="500"
              location="bottom"
              activator="parent"
              transition="scroll-x-transition"
            >
              <span>Click to reset the price range</span>
            </VTooltip>
            Price per hour: RM{{ sliderValues[0] }} - RM{{ sliderValues[1] }}
          </p>

      </VCol>

      <VCol cols="12" md="4">
        <VTextField
          v-model="searchValueAllServices"
          placeholder="Search"
          label="Search name or description of service"
          clearable
          dense
        />
      </VCol>

      
      
    </VRow>
  </div>
    <div class="box-style">
      <VRow class="mt-2 mr-2 mb-1 ml-2">
        <VCol 
          v-if="user.isSeller"
          cols="12" md="4">
          <VBtn @click="ownServices=true">
            <VTooltip
              location="top"
              activator="parent"
              transition="scroll-x-transition"
            >
              <span>Click to view your own services</span>
          </VTooltip>
            Own Services
          </VBtn>
        </VCol>
        <VCol v-else cols="12" md="4" />
        <VCol cols="12" md="4" />
        <VCol cols="12" md="4" class="d-flex justify-end">
      <VMenu open-on-hover>
      <template #activator="{ props }">
        <VBtn v-bind="props" color="secondary">
          <VIcon icon="ri-sort-desc"  class="mr-2"/>
          Sort By
        </VBtn>
        </template>

        <VList>
          <VListItem
            v-for="service in sortBy"
            :key="service.title"
            @click="sortService = service.value"
          >
            <VListItemTitle>{{ service.title }}</VListItemTitle>
          </VListItem>
        </VList>
      </VMenu>
    </VCol>
      </VRow>
      
    <!-- Item List -->

      <div class="item-list-container">
        <ServiceList
          :allServices="allServices"
          :allServicesLoad="allServicesLoad"
        />
      </div>
      <VCardText>
        <VRow>
          <VCol cols="12" md="4" />
          <VCol cols="12" md="4">
            <VPagination
              v-model="currentPageAllServices"
              variant="outlined"
              :length="totalPagesAllServices"
              rounded="circle"
            />
          </VCol>
          <VCol cols="12" md="4" />
        </VRow>
      </VCardText>
    </div>



  <!--Own Services Dialog-->
  <VDialog
    v-model="ownServices"
    max-width="1200"
  >

  <div class="box-style">

    <!-- 'Own Selling Item Table -->
    <VRow>
      <VCol cols="12" md="3">
        <div class="mt-5 ml-5 mb-4">
          <VBtn @click="addPictureBox=true">
            Provide New Service
          </VBtn>
        </div>
      </VCol>
      <VCol cols="12" md="3"/>
      <VCol cols="12" md="3"/>
      <VCol cols="12" md="3">
        <VTextField
        class="mt-5 mr-5 mb-4"
          v-model="searchValue"
          placeholder="Search"
          label="Search name of the service"
          clearable
          dense
        />
      </VCol>
    </VRow>

    <div class="table-style">
      <TableService
        :services="services"
        :serviceLoad="serviceLoad"
        :allServicesLoad="allServicesLoad"
        :types="types"
      />
    </div>

    <VCardText>
      <VRow>
        <VCol cols="12" md="2">
          <VCombobox
            v-model="rowPerPage"
            :items="[5, 10, 15, 20]"
            label="Rows per page"
            dense
          />
        </VCol>
        <VCol cols="12" md="6"/>
          
        <VCol cols="12" md="4">
          <VPagination
            v-model="currentPage"
            variant="outlined"
            :length="totalPages"
            rounded="circle"
          />
        </VCol>
      </VRow>
    </VCardText>
  </div>
</VDialog>




<!--Add New Service-->
<VDialog
    v-model="addNewDialog"
    max-width="800"
  >
    <!-- Dialog Content -->
    <VCard title="Add New Service">

      <VCardText>
        <VForm 
        ref="refForm" 
        @submit.prevent>

        <VRow>
          <!-- 👉 First Name -->
          <VCol cols="12">
            <VRow no-gutters>
              <VCol
                cols="12"
                md="3"
              >
                <label for="firstNameHorizontalIcons">Service</label>
              </VCol>

              <VCol
                cols="12"
                md="9"
              >
                <VTextField
                  v-model="name"
                  prepend-inner-icon="ri-shopping-bag-4-line"
            
                  placeholder="Rolex"
                  :rules="[requiredValidator]"
                />
              </VCol>
            </VRow>
          </VCol>

          <VCol cols="12">
            <VRow no-gutters>
              <VCol
                cols="12"
                md="3"
              >
                <label for="firstNameHorizontalIcons">Description</label>
              </VCol>

              <VCol
                cols="12"
                md="9"
              >
                <VTextarea
                  v-model="description"
                  prepend-inner-icon="ri-information-2-line"
                  no-resize
                  placeholder="Describe your service"
                  :rules="[requiredValidator]"
                />
              </VCol>
            </VRow>
          </VCol>

        <VCol cols="12">
            <VRow no-gutters>
              <VCol
                cols="12"
                md="3"
              >
                <label for="firstNameHorizontalIcons">Type</label>
              </VCol>

              <VCol
                cols="12"
                md="9"
              >
              <VSelect
                v-model="type"
                :items="types"
                
                prepend-inner-icon="ri-price-tag-3-line"
                placeholder="Select Type"
                variant="solo"
              />
            </VCol>
          </VRow>
        </VCol>

        <VCol cols="12">
            <VRow no-gutters>
              <VCol
                cols="12"
                md="3"
              >
                <label for="firstNameHorizontalIcons">Price Per Hour</label>
              </VCol>

              <VCol
                cols="12"
                md="9"
              >
                <VTextField
                  v-model="pricePerHour"
                  prepend-inner-icon="ri-money-dollar-circle-line"
                  
                  placeholder="100.00"
                  :rules="[requiredValidator, priceValidator]"
                />
              </VCol>
            </VRow>
          </VCol>

          <!-- 👉 submit and reset button -->
          <VCol
            offset-md="3"
            cols="12"
            md="9"
            class="d-flex gap-4"
          >
          <VBtn
              color="info"
              @click="addNewDialog=false"
              variant="tonal"
            >
              Back
            </VBtn>
            <VBtn
              color="secondary"
              type="reset"
              variant="tonal"
            >
              Reset
            </VBtn>
            
            <VBtn
              type="submit"
              @click="$refs.refForm.validate().then(() => createService())"
            >
              Publish
            </VBtn>
          </VCol>
        </VRow>
      </VForm>
      </VCardText>
    </VCard>
  </VDialog>




  <!-- Add Picture Dialog -->
  <VDialog
    v-model="addPictureBox"
    max-width="600"
  >
    <VCard title="Add Item Pictures">
      <div class="upload-container">
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
          <div v-if="pictures.length > 0" class="uploaded-images">
            <p class="upload-text">Selected images: </p>
          <div v-for="(picture, index) in pictures" :key="index" class="uploaded-image">
            <img :src="picture.url" alt="Uploaded Image" />
            <VBtn @click="removeImage(index)" icon="ri-close-line" class="remove-btn"/>
          </div>
        </div>
        </div>
      </div>
   
      <VCardActions>
        <div class="button-container">
          <VBtn color="secondary" @click="addPictureBox = false">Cancel</VBtn>
          <VBtn color="success" @click="addNewDialog=true">Next</VBtn>
        </div>
      </VCardActions>
    </VCard>
  </VDialog>



  <!-- Add Picture Dialog -->
  <VDialog
    v-model="addPictureBox"
    max-width="600"
  >
    <VCard title="Add Item Pictures">
      <div class="upload-container">
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
          <div v-if="pictures.length > 0" class="uploaded-images">
            <p class="upload-text">Selected images: </p>
          <div v-for="(picture, index) in pictures" :key="index" class="uploaded-image">
            <img :src="picture.url" alt="Uploaded Image" />
            <VBtn @click="removeImage(index)" icon="ri-close-line" class="remove-btn"/>
          </div>
        </div>
        </div>
      </div>
   
      <VCardActions>
        <div class="button-container">
          <VBtn color="secondary" @click="addPictureBox = false">Cancel</VBtn>
          <VBtn color="success" @click="addNewDialog=true">Next</VBtn>
        </div>
      </VCardActions>
    </VCard>
  </VDialog>



  <!--Snackbar-->
  <VSnackbar
      v-model="isAddAlert"
      location="top end"
      transition="scale-transition"
      color="success"
    >
    <VIcon size="20" class="me-2">ri-checkbox-circle-line</VIcon>
      New service has been successfully registered.
    </VSnackbar>
  <VSnackbar
      v-model="hasErrorAlert"
      location="top end"
      transition="scale-transition"
      color="error"
    >
    <VIcon size="20" class="me-2">ri-alert-line</VIcon>
      {{ errorMessages }}
    </VSnackbar>


</template>



<style scoped>

.box-style {
  padding: 1.5px; /* Padding around the table */
  border: 0.4px solid #282828;
  border-radius:10px;
  background-color: #fff; /* White background color */
  box-shadow: 0 0 10px rgba(0, 0, 0, 15%); /* Drop shadow */
  margin-block-end: 15px
}

.item-list-container {
  block-size: 550px; /* Set the desired height */
  overflow-y: auto; /* Enable vertical scrolling */
}

.table-style{
  padding: 0.5px; /* Padding around the table */
  box-shadow: 0 0 10px rgba(0, 0, 0, 15%); /* Drop shadow */
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

</style>
