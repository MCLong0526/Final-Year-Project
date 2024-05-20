<script setup>
import { requiredValidator } from '@/@core/utils/validators';
import axios from 'axios';


const props = defineProps({
  items: {
    type: Object,
    required: true,
  },
  itemsLoad: {
    type: Function,
    required: true,
  },
  allItemLoad: {
    type: Function,
    required: true,
  },
  conditions: {
    type: Array,
    required: true,
  },
  types: {
    type: Array,
    required: true,
  },
});

const defineEmits = defineEmits(['update:items']);
const deleteDialog = ref(false);
const clickedItem = ref({});
const editItemDialog = ref(false);
const currentTab = ref('tab-1');
const isDeleteAlert = ref(false);
const isEditAlert = ref(false);
const hasErrorAlert = ref(false);
const errorMessages = ref('');
const soldDialog = ref(false);
const openSoldAtWhereDialog = ref(false);
const isSuccessAlertSold = ref(false);
const isSuccessAlertAvailable = ref(false);

const columnRadio = ref('');
const changeSoldItem = ref({});


const openDeleteDialog = (item) => {
  deleteDialog.value = true;
  clickedItem.value = item;
};

const openEditDialog = (item) => {
  editItemDialog.value = true;
  clickedItem.value = Object.assign({}, item);

  // change the picture_path to base64 format
  clickedItem.value.pictures.forEach((image, index) => {
    if (image.picture_path.startsWith('http') || image.picture_path.startsWith('localhost://')) {
      fetch(image.picture_path)
        .then((response) => response.blob())
        .then((blob) => {
          const reader = new FileReader();
          reader.onload = (e) => {
            image.picture_path = e.target.result;
            if (index === clickedItem.value.pictures.length - 1) {
              // All images converted, do nothing
            }
          };
          reader.readAsDataURL(blob);
        });
    } else if (image.picture_path.startsWith('localhost')) {
      // Update the picture_path if it starts with 'localhost'
      image.picture_path = 'data:image;base64,' + image.picture_path;
    }
  });
};

//delete item
const deleteItem = () => {
  axios.delete(`/api/items/delete/${clickedItem.value.item_id}`)
    .then((response) => {
      deleteDialog.value = false;
      isDeleteAlert.value = true;
      props.itemsLoad();
      props.allItemLoad();
    }).catch((error) => {
      errorMessages.value = error.response.data.message;
      hasErrorAlert.value = true;
      console.log(error);
    });
};

//edit item
const editItem = () => {
  axios.put(`/api/items/update/${clickedItem.value.item_id}`, {
    name: clickedItem.value.name,
    description: clickedItem.value.description,
    condition: clickedItem.value.condition,
    type: clickedItem.value.type,
    price: clickedItem.value.price,
    quantity: clickedItem.value.quantity,
    images: clickedItem.value.pictures,
    availability: clickedItem.value.availability,
  
  })
    .then((response) => {
      editItemDialog.value = false;
      isEditAlert.value = true;
      props.itemsLoad();
      props.allItemLoad();
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
  const hasExistingImages = clickedItem.value.pictures.some(image => !image.isNew);

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
        clickedItem.value.pictures.push({ picture_path: picturePath, file, isNew: !isBase64 });

        // Convert existing images if any
        if (hasExistingImages) {
          const existingImages = clickedItem.value.pictures.filter(image => !image.isNew);
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
  const removedImage = clickedItem.value.pictures.splice(index, 1)[0];
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


//availability change
const availableButton = (item_id) => {
  axios.put(`/api/items/update-status/${item_id}`, {
    availability: 'Available',
  })
    .then((response) => {
      console.log(response.data);
      isSuccessAlertAvailable.value = true;
      props.itemsLoad();
    }).catch((error) => {
      console.log(error);
    });
};

const updateAvailability = (item_id) => {
  axios.put(`/api/items/update-status/${item_id}`, {
    availability: 'Sold',
  })
    .then((response) => {
      console.log(response.data);
      isSuccessAlertSold.value = true;

      props.itemsLoad();

    }).catch((error) => {
      console.log(error);
    });
};

const updateStatusInfo = (item_id) => {
  let status_info = '';
  if (columnRadio.value === 'radio-1') {
    status_info = 'Sold at this platform';
  } else if (columnRadio.value === 'radio-2') {
    status_info = 'Sold at other platform';
  } else if (columnRadio.value === 'radio-3') {
    status_info = 'Not sold yet';
  } else if (columnRadio.value === 'radio-4') {
    status_info = 'Select not to answer';
  }
  axios.put(`/api/items/update-statusinfo/${item_id}`, {
    status_info: status_info,
  })
    .then((response) => {
      console.log(response.data);
      props.itemsLoad();
    }).catch((error) => {
      console.log(error);
    });
};



const openSoldDialog = (item) => {
  changeSoldItem.value = item;
  soldDialog.value = true;
};

const markAsSold = () => {
  updateAvailability(changeSoldItem.value.item_id);
  updateStatusInfo(changeSoldItem.value.item_id);
  openSoldAtWhereDialog.value = false;
  soldDialog.value = false;
};



//validations
const priceValidator = (value) => {
  if (!value) {
    return 'Price is required';
  }

  if (isNaN(value)) {
    return 'Price must be a number';
  }

  if (value <= 0) {
    return 'Price must be greater than 0';
  }

  return true;
};

</script>

<template>
 
  <VTable
    height="250"
    fixed-header
    v-if="items.length > 0"
  >
    <thead>
      <tr>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-shopping-bag-4-line" />
          Product
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-price-tag-3-line" />
          Type
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-thumb-up-line" />
          Condition
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-survey-line" />
          Quantity
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-money-dollar-circle-line" />
          Price
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-hand-heart-line" />
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
        v-for="item in items"
        :key="item.item_id"
      >
      <td class="text-center" style="display: flex; align-items: center;">
        <VAvatar
          v-if="item.pictures.length > 0 && item.pictures"
          size="32"
          rounded="sm"
          style="margin-inline: 30px 8px;"
          class="square-avatar"
        >
          <VImg
            :src="item.pictures[0].picture_path"
          />
        </VAvatar>
        <template>
          <VAvatar
            size="32"
            color="primary"
            rounded="sm"
            style="margin-inline: 40px 8px;"
            class="square-avatar"
          >
            <VIcon>ri-image-add-line</VIcon>
          </VAvatar>
        </template>
        {{ item.name }}
      </td>

        <td class="text-center">
          {{ item.type }}
        </td>
        <td class="text-center">
          {{ item.condition }}
        </td>
        <td class="text-center">
          <VChip v-if="item.quantity > 0" color="primary" size="small">
            {{ item.quantity }}
          </VChip>
          <VChip v-else color="secondary">
          {{ item.quantity }}
          </VChip>
        </td>
        <td class="text-center">
          RM {{ item.price }}
        </td>
        <td class="text-center">
          <VBtn 
            v-if="item.availability=='Available'" 
            variant="text" 
            color="success" 
            rounded="pill"
            @click="openSoldDialog(item)"
          >
            <VTooltip
              open-delay="500"
              location="top"
              activator="parent"
              transition="scroll-y-transition"
            >
              <span>Click to mark as Sold</span>
            </VTooltip>
            <VIcon
              start
              icon="ri-checkbox-circle-line"
            />
            {{ item.availability }}
          </VBtn>
          <VBtn 
            v-else 
            color="error" 
            variant="text" 
            rounded="pill"
            @click="availableButton(item.item_id)"
          >
            <VTooltip
              open-delay="500"
              location="top"
              activator="parent"
              transition="scroll-y-transition"
            >
              <span>Click to mark as Available</span>
            </VTooltip>
            <VIcon
              start
              icon="ri-close-circle-line"
            />
            {{ item.availability }}
          </VBtn>
          
        </td>
       
        <td class="text-center">

          
          <VBtn
            color="info"
            style="margin-inline: 8px 3px"
            size="small"
            @click="openEditDialog(item)"
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
            @click="openDeleteDialog(item)"
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
    height="140"
    fixed-header
    v-else
  >
    <thead>
      <tr>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-shopping-bag-4-line" />
          Product
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-thumb-up-line" />
          Condition
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-price-tag-3-line" />
          Type
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-survey-line" />
          Quantity
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-money-dollar-circle-line" />
          Price
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-hand-heart-line" />
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
        <td class="text-center" colspan="7">
          No items found.
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
    <VCard title="Delete Item">    
      <VCardText>
        <VAlert
          color="error"
          icon="ri-alert-line"
          variant="tonal"
        >
        Are you sure you want to delete product: <strong>{{ clickedItem.name }}</strong> ?
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
          @click="deleteItem"
        >
          Delete
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>



  <!--Edit Dialog-->
  <VDialog
    v-model="editItemDialog"
    max-width="800"
    scrollable
    max-height="900"
  >
    <!-- Dialog Content -->
    <VCard title="Edit Selling Item">

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
          <span>Item pictures</span>
        </VTab>

        <VTab value="tab-2">
          <VIcon
            icon="ri-information-2-line"
            class="mb-2"
          />
          <span>Item Information</span>
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
              <div v-if="clickedItem.pictures.length > 0" class="uploaded-images">
                <p class="upload-text">Selected images: </p>
              <div v-for="(image, index) in clickedItem.pictures" :key="index" class="uploaded-image">
                <img :src="image.picture_path" alt="Uploaded Image" />
                <VBtn @click="removeImage(index)" icon="ri-close-line" class="remove-btn"/>
              </div>
            </div>
            </div>
          </div>
        
            <VCardActions>
              <div class="button-container">
                <VBtn color="secondary" @click="editItemDialog = false">Cancel</VBtn>
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
                    <label for="firstNameHorizontalIcons">Item Name</label>
                  </VCol>

                  <VCol
                    cols="12"
                    md="9"
                  >
                    <VTextField
                      v-model="clickedItem.name"
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
                      v-model="clickedItem.description"
                      prepend-inner-icon="ri-information-2-line"
                      no-resize
                      placeholder="Describe the item, e.g., color, size, material, features..."
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
                    <label for="firstNameHorizontalIcons">Condition</label>
                  </VCol>

                  <VCol
                    cols="12"
                    md="9"
                  >
                  <VSelect
                    v-model="clickedItem.condition"
                    :items="conditions"
              
                    placeholder="Select Condition"
                    prepend-inner-icon="ri-thumb-up-line"
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
                    <label for="firstNameHorizontalIcons">Type</label>
                  </VCol>

                  <VCol
                    cols="12"
                    md="9"
                  >
                  <VSelect
                    v-model="clickedItem.type"
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
                    <label for="firstNameHorizontalIcons">Quantity</label>
                  </VCol>

                  <VCol
                    cols="12"
                    md="9"
                  >
                    <VTextField
                      v-model="clickedItem.quantity"
                      prepend-inner-icon="ri-survey-line"
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
                    <label for="firstNameHorizontalIcons">Price</label>
                  </VCol>

                  <VCol
                    cols="12"
                    md="9"
                  >
                    <VTextField
                      v-model="clickedItem.price"
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
                  @click="$refs.refForm.validate().then(() => editItem())"
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


  <!--Sold Dialog-->
  <VDialog
    v-model="soldDialog"
    persistent
    class="v-dialog-sm"
  >
  
      <!-- Dialog Content -->
      <VCard title="Mark as Sold">
  
        <VCardText>
          <VAlert
            color="success"
            icon="ri-checkbox-circle-line"
            variant="tonal"
          >
            Are you sure you want to mark this item as sold?
          </VAlert>
        </VCardText>
  
        <VCardActions>
          <VSpacer />
          <VBtn
            color="secondary"
            @click="soldDialog = false"
          >
            Cancel
          </VBtn>
          <VBtn
            color="success"
            @click="openSoldAtWhereDialog = true"
          >
            Mark as Sold
          </VBtn>
        </VCardActions>
      </VCard>
  </VDialog>

  <!--Sold At Where Dialog-->
  <VDialog
    v-model="openSoldAtWhereDialog"
    persistent
    class="v-dialog-sm"
  >
  
      <!-- Dialog Content -->
      <VCard title="Have you sold this item?">
  
        <VCardText>
          <VAlert
            color="success"
            icon="ri-checkbox-circle-line"
            variant="tonal"
            class="mb-3"
          >
            Please select where you have sold this item.
          </VAlert>
          <VRadioGroup v-model="columnRadio">
            <VRadio
              label="Yes, sold at this platform"
              value="radio-1"
            />
            <VRadio
              label="Yes, sold at other platform"
              value="radio-2"
            />
            <VRadio
              label="No, not sold yet"
              value="radio-3"
            />
            <VRadio
              label="Select not to answer"
              value="radio-4"
            />
          </VRadioGroup>
        </VCardText>
  
        <VCardActions>
          <VSpacer />
          <VBtn
            color="secondary"
            @click="openSoldAtWhereDialog = false"

          >
            Back
          </VBtn>
          <VBtn
            color="success"
            @click="markAsSold"
          >
            Submit
          </VBtn>

        </VCardActions>
      </VCard>
  </VDialog>



  <!--Snackbar-->
  <VSnackbar
      v-model="isDeleteAlert"
      location="top end"
      transition="scale-transition"
      color="success"
    >
    <VIcon size="20" class="me-2">ri-checkbox-circle-line</VIcon>
      Item <strong>{{ clickedItem.name }}</strong> has been successfully deleted.
    </VSnackbar>

    <VSnackbar
      v-model="isEditAlert"
      location="top end"
      transition="scale-transition"
      color="success"
    >
      Item <strong>{{ clickedItem.name }}</strong> information has been successfully edited.
    </VSnackbar>

    <VSnackbar
      v-model="isSuccessAlertSold"
      location="top end"
      transition="scale-transition"
      color="success"
    >
      Item <strong>{{ clickedItem.name }}</strong> has been successfully marked as sold.
    </VSnackbar>
    <VSnackbar
      v-model="isSuccessAlertAvailable"
      location="top end"
      transition="scale-transition"
      color="success"
    >
      Item <strong>{{ clickedItem.name }}</strong> has been successfully marked as available.
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

