<script setup>
import { requiredValidator } from '@/@core/utils/validators';
import TableService from '@/components/Service/ServiceTable.vue';
import { useAuthStore } from '@/plugins/store/AuthStore';
import axios from 'axios';
import { debounce } from 'lodash';

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

const name = ref('');
const description = ref('');
const type = ref(null);
const pricePerHour = ref('');
const pictures = ref([]);

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
    type.value = '';
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

serviceLoad()
</script>

<template>
  <div class="box-style">

<!-- 'Own Selling Item Table -->
<VRow>
  <VCol cols="12" md="3">
    <div class="mt-1 ml-2">
      <VBtn @click="addPictureBox=true">
        Provide New Service
      </VBtn>
    </div>
  </VCol>
  <VCol cols="12" md="3"/>
  <VCol cols="12" md="3"/>
  <VCol cols="12" md="3">
    <VTextField
    class="mb-2 mr-2"
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
  box-shadow: 0 0 10px rgba(0, 0, 0, 15%); /* Drop shadow */
  margin-block-end: 15px;
}

.item-list-container {
  block-size: 550px; /* Set the desired height */
  overflow-y: auto; /* Enable vertical scrolling */
}

.table-style{
  padding: 0.5px; /* Padding around the table */
  background-color: #848383; /* White background color */
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

.box-style {
  padding: 10px; /* Padding around the text */
  border: 1.5px solid #d3d3d3;
  border-radius: 5px; /* Rounded corners */
  background-color: rgba(255, 255, 255, 53.7%); /* Light gray background color */
}
</style>
