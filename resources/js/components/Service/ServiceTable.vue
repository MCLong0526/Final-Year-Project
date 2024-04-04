<script setup>
import { requiredValidator } from '@/@core/utils/validators';
import axios from 'axios';
const props = defineProps({
  services: {
    type: Object,
    required: true,
  },
  serviceLoad: {
    type: Function,
    required: true,
  },
  types: {
    type: Array,
    required: true,
  },

});

const deleteDialog = ref(false);
const clickedService = ref(null);
const isDeleteAlert = ref(false);
const editServiceDialog = ref(false);
const currentTab = ref('tab-1');
const isEditAlert = ref(false);
const errorMessages = ref('');
const hasErrorAlert = ref(false);
const unavailableDialog = ref(false);

//availability change
const unavailableButton = (availability) => {
  if (availability === 'Available') {
    unavailableDialog.value = true;
    clickedService.value.availability = 'Unavailable';
  } 
  
};

//edit item
const openEditDialog = (service) => {
  editServiceDialog.value = true;
  clickedService.value = Object.assign({}, service);

  // change the picture_path to base64 format
  clickedService.value.pictures.forEach((picture, index) => {
    if (picture.picture_path.startsWith('http') || picture.picture_path.startsWith('localhost://')) {
      fetch(picture.picture_path)
        .then((response) => response.blob())
        .then((blob) => {
          const reader = new FileReader();
          reader.onload = (e) => {
            picture.picture_path = e.target.result;
            if (index === clickedService.value.pictures.length - 1) {
              // All pictures converted, do nothing
            }
          };
          reader.readAsDataURL(blob);
        });
    } else if (picture.picture_path.startsWith('localhost')) {
      // Update the picture_path if it starts with 'localhost'
      picture.picture_path = 'data:image;base64,' + picture.picture_path;
    }
  });
};


const editService = () => {

  axios.put(`/api/services/update/${clickedService.value.service_id}`, {
    name: clickedService.value.name,
    description: clickedService.value.description,
    type: clickedService.value.type,
    price_per_hour: clickedService.value.price_per_hour,
    pictures: clickedService.value.pictures,
    availability: clickedService.value.availability,
  
  })
    .then((response) => {
      editServiceDialog.value = false;
      props.serviceLoad();
    }).catch((error) => {
      errorMessages.value = error.response.data.message;
      hasErrorAlert.value = true;
      console.log(error);
    });
};
//edit item end


//delete item
const openDeleteDialog = (service) => {
  clickedService.value = service;
  deleteDialog.value = true;
};

const deleteService = () => {
  axios.delete(`/api/services/delete/${clickedService.value.service_id}`)
    .then(() => {
      deleteDialog.value = false;
      isDeleteAlert.value = true;
      props.serviceLoad();
     
      
    })
    .catch((error) => {
      console.log(error);
    });
};
//delete item end


//image part:
const handleDrop = (event) => {
  event.preventDefault();
  const files = event.dataTransfer.files;
  handleFiles(files);
};

const handleFiles = (files) => {
  const hasExistingImages = clickedService.value.pictures.some(image => !image.isNew);

  for (let i = 0; i < files.length; i++) {
    const file = files[i];
    if (file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = (e) => {
        const base64Data = e.target.result;
        const isBase64 = base64Data.startsWith('data:image');
        let picturePath = base64Data;

        // Check if the image is already in base64 format
        if (!isBase64) {
          picturePath = base64Data;
        }

        // Add new image to the pictures array
        clickedService.value.pictures.push({ picture_path: picturePath, file, isNew: !isBase64 });

        // Convert existing images if any
        if (hasExistingImages) {
          const existingImages = clickedService.value.pictures.filter(image => !image.isNew);
          existingImages.forEach((existingImage, index) => {
            if (existingImage.picture_path.startsWith('http') || existingImage.picture_path.startsWith('localhost://')) {
              fetch(existingImage.picture_path)
                .then((response) => response.blob())
                .then((blob) => {
                  const reader = new FileReader();
                  reader.onload = (e) => {
                    existingImage.picture_path = e.target.result;
                    if (index === existingImages.length - 1) {
                      // All existing images converted, do nothing
                    }
                  };
                  reader.readAsDataURL(blob);
                });
            } else if (existingImage.picture_path.startsWith('localhost')) {
              // Update the picture_path if it starts with 'localhost'
              existingImage.picture_path = 'data:image;base64,' + base64Data;
            }
          });
        }
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
  const removedImage = clickedService.value.pictures.splice(index, 1)[0];
  if (!removedImage.isNew) {
    // If the removed image is not new, update its picture_path to base64 format
    fetch(removedImage.picture_path)
      .then((response) => response.blob())
      .then((blob) => {
        const reader = new FileReader();
        reader.onload = (e) => {
          removedImage.picture_path = e.target.result;
        };
        reader.readAsDataURL(blob);
      });
  }
};
//image part end


//price validation
const priceValidator = (value) => {
  if (!value) return 'Price is required';
  if (isNaN(value)) return 'Price must be a number';
  return true;
};



</script>

<template>
  <VTable
    v-if="services.length > 0"
    height="250"
    fixed-header
  >
    <thead>
      <tr>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-shopping-bag-4-line" />
          Service Name
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-price-tag-3-line" />
          Type
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-money-dollar-circle-line" />
          Price Per Hour
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-time-line" />
          Availability
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-settings-3-line" />
          Action
        </th>

      </tr>
    </thead>

    <tbody>
      <tr
        v-for="service in services"
        :key="service.service_id"
      >
      <td class="text-center" style="display: flex; align-items: center;">
        <VAvatar
          v-if="service.pictures.length > 0 && service.pictures"
          size="32"
          rounded="sm"
          style="margin-inline: 30px 8px;"
          class="square-avatar"
        >
          <VImg
            :src="service.pictures[0].picture_path"
          />
        </VAvatar>
     
          <VAvatar
          v-else
            size="32"
            color="primary"
            rounded="sm"
            style="margin-inline: 40px 8px;"
            class="square-avatar"
          >
            <VIcon>ri-image-add-line</VIcon>
          </VAvatar>
  
        {{ service.name }}
      </td>
        <td class="text-center">
          {{ service.type }}
        </td>
        <td class="text-center">
          RM {{ service.price_per_hour }}/hour
        </td>
        <td class="text-center">
          <VChip v-if="service.availability=='Available'" color="success">
            {{ service.availability }}
            <VIcon icon="ri-checkbox-circle-line" style="margin-inline-start: 5px;" />
          </VChip>
          <VChip v-else color="error">
            {{ service.availability }}
            <VIcon icon="ri-close-circle-line" style="margin-inline-start: 5px;" />
          </VChip>
          
        </td>
        <td class="text-center">
          <VBtn
            color="info"
            style="margin-inline: 15px 3px"
            size="small"
            @click="openEditDialog(service)"
            
          >
            <VIcon
              icon="ri-pencil-line"
            />
            <VTooltip
                open-delay="500"
                location="top"
                activator="parent"
                transition="scroll-y-transition"
              >
                <span>Edit</span>
              </VTooltip>
          </VBtn>
          <VBtn
            color="error"
            style="margin-inline: 5px 5px;"
            size="small"
            @click="openDeleteDialog(service)"
          >
            <VIcon
              icon="ri-delete-bin-line"
            />
            <VTooltip
                open-delay="500"
                location="top"
                activator="parent"
                transition="scroll-y-transition"
              >
                <span>Delete</span>
              </VTooltip>
          </VBtn>
        </td>
      </tr>
    </tbody>
  </VTable>

  <VTable
    v-else
    height="140"
    fixed-header
  >
    <thead>
      <tr>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-shopping-bag-4-line" />
          Service Name
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-price-tag-3-line" />
          Type
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-money-dollar-circle-line" />
          Price Per Hour
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-time-line" />
          Availability
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-settings-3-line" />
          Action
        </th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="text-center" colspan="5">
          <VAlert  
            variant="tonal"
            type="warning"
            class="mt-2"
            color="primary"
            closable
            dense
          >
            No service available. Please add a new service.
          </VAlert>
        </td>
      </tr>
    </tbody>
  </VTable>

  <!--Delete Dialog-->
  <VDialog
    v-model="deleteDialog"
    persistent
    class="v-dialog-sm"
  >
    <!-- Dialog Content -->
    <VCard title="Delete Service"> 
      <VCardText>
        <VAlert
          color="error"
          icon="ri-alert-line"
          variant="tonal"
        >
        Are you sure you want to delete service: <strong>{{ clickedService.name }}</strong>?
        </VAlert>
      </VCardText>

      <VCardActions>
        <VSpacer />
        <VBtn
          color="secondary"
          @click="deleteDialog = false"
        >
          Cancel
        </VBtn>
        <VBtn
          color="error"
          @click="deleteService"
        >
          Delete
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>


  <!--Edit Dialog-->
  <VDialog
    v-model="editServiceDialog"
    max-width="800"
    scrollable
    max-height="800"
  >
    <!-- Dialog Content -->
    <VCard title="Edit Service">

      <VTabs
        v-model="currentTab"
        grow
        stacked
      >
        <VTab value="tab-1">
          <VIcon
            icon="ri-image-add-line"
            class="mb-2"
          />
          <span>Service pictures</span>
        </VTab>

        <VTab value="tab-2">
          <VIcon
            icon="ri-information-2-line"
            class="mb-2"
          />
          <span>Service Information</span>
        </VTab>
      </VTabs>


    
      <!--edit Pictures-->
      <VWindow
        v-model="currentTab"
        class="mt-5"
      >
        <VWindowItem
          value="tab-1"
        >
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
              <div v-if="clickedService.pictures.length > 0" class="uploaded-images">
                <p class="upload-text">Selected images: </p>
              <div v-for="(picture, index) in clickedService.pictures" :key="index" class="uploaded-image">
                <img :src="picture.picture_path" alt="Uploaded Image" />
                <VBtn @click="removeImage(index)" icon="ri-close-line" class="remove-btn"/>
              </div>
            </div>
            </div>
          </div>
        
            <VCardActions>
              <div class="button-container">
                <VBtn color="secondary" @click="editServiceDialog = false">Cancel</VBtn>
                <VBtn color="success" @click="currentTab='tab-2'">Next</VBtn>
              </div>
            </VCardActions>
        </VWindowItem>


        <!--edit Information-->
        <VWindowItem
          value="tab-2"
        
        >
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
                    <label for="firstNameHorizontalIcons">Service Name</label>
                  </VCol>

                  <VCol
                    cols="12"
                    md="9"
                  >
                    <VTextField
                      v-model="clickedService.name"
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
                      v-model="clickedService.description"
                      prepend-inner-icon="ri-information-2-line"
                      no-resize
                      placeholder="Describe the service"
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
                    v-model="clickedService.type"
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
                    <label for="firstNameHorizontalIcons">Price</label>
                  </VCol>

                  <VCol
                    cols="12"
                    md="9"
                  >
                    <VTextField
                      v-model="clickedService.price_per_hour"
                      prepend-inner-icon="ri-money-dollar-circle-line"
                      
                      placeholder="100.00"
                      :rules="[requiredValidator, priceValidator]"
                    />
                  </VCol>
                </VRow>
              </VCol>

              <!-- 👉 Availability -->
           <VCol cols="12">
            <VRow no-gutters>
              <VCol
                cols="12"
                md="3"
              >
                <label for="status">Availability</label>
              </VCol>
              <VBtn
                v-if="clickedService.availability === 'Available'"
                color="success"
                variant="tonal"
                rounded="pill"
                @click="unavailableButton(clickedService.availability)"
              >
                Available
                <VIcon
                  icon="ri-checkbox-circle-line"
                  style="margin-inline-start: 5px;"
                />
                <VTooltip
                  activator="parent"
                  open-delay="500"
                  location="end"
                >
                  Click to make the service unavailable
                </VTooltip>
              </VBtn>
              <VBtn
                v-else
                color="error"
                variant="tonal"
                rounded="pill"
                @click="clickedService.availability = 'Available'"
              >
                Unavailable
                <VIcon
                  icon="ri-close-circle-line"
                  style="margin-inline-start: 5px;"
                />
                <VTooltip
                  activator="parent"
                  open-delay="500"
                  location="end"
                >
                  Click to make the service available
                </VTooltip>
              </VBtn>
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
                  @click="currentTab='tab-1'"
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
                  @click="$refs.refForm.validate().then(() => editService())"
                >
                  Edit
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
          </VCardText>
        </VWindowItem>
      </VWindow>

      
    </VCard>
  </VDialog>

  <!--Unavailable Dialog-->
  <VDialog
    v-model="unavailableDialog"
    persistent
    class="v-dialog-sm"
  >

    <!-- Dialog Content -->
    <VCard title="Service Unavailable">

      <VCardText>
        <VAlert
          color="error"
          icon="ri-alert-line"
          variant="tonal"
        >
          This service is currently unavailable.
        </VAlert>
      </VCardText>

      <VCardActions>
        <VSpacer />
        
        <VBtn 
          color="error"
          @click="unavailableDialog = false"
        >
          I accept
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>


  <VSnackbar
      v-model="isDeleteAlert"
      location="top end"
      transition="scale-transition"
      color="success"
    >
    <VIcon size="20" class="me-2">ri-checkbox-circle-line</VIcon>
      Service <strong>{{ clickedService.name }}</strong> has been successfully deleted.
    </VSnackbar>

    <VSnackbar
      v-model="isEditAlert"
      location="top end"
      transition="scale-transition"
      color="success"
    >
      Service <strong>{{ clickedService.name }}</strong> information has been successfully edited.
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

.square-avatar {
  aspect-ratio: 1 / 1; /* Make the avatar square */
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
  gap: 640px;
}

</style>

