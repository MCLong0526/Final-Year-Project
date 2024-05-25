<script setup>
import { requiredValidator } from '@/@core/utils/validators';
import axios from 'axios';
import { debounce } from 'lodash';
import { ref } from 'vue';
import TableItem from '/resources/js/components/Item/ItemTable.vue';


const user = ref([]);
const addNewDialog = ref(false);
const name = ref('');
const description = ref('');
const price = ref('');
const condition = ref(null);
const type = ref(null);
const quantity = ref('');
const items = ref([]);
const rowPerPage = ref(5);
const rowPerPageAllItems = ref(8);
const currentPage = ref(1);
const currentPageAllItems = ref(1);
const totalPages = ref(0);
const totalPagesAllItems = ref(0);
const searchValue = ref('');
const addPictureBox = ref(false);
const images = ref([]);
const allItems = ref([]);
const typeSearch = ref([]);
const sortItem = ref(null);
const conditionSearch = ref(null);
const searchValueAllItems = ref('');
const isAddAlert = ref(false);
const hasErrorAlert = ref(false);
const errorMessages = ref('');
const ownSellingItems = ref(false);
const sliderValues = ref([0, 200]);

const sortBy = [
  { title: 'Price:Low to High', value: 'Price:Low to High' }, 
  { title: 'Price:High to Low', value: 'Price:High to Low' },
  { title:'Item name: A to Z', value: 'name: A to Z'},
  { title:'Item name: Z to A', value: 'name: Z to A'},
];

const conditions = [
  'New',
  'Used (Like New)',
  'Used (Good)',
  'For parts or not working',
];

const types = [
  'Fashion',
  'Electronics',
  'Furniture',
  'Vehicles',
  'Musical Instruments',
  'Sports Equipment',
  'Home Appliances',
  'Books and Stationery',
  'Health and Beauty',
  'Toys and Games',
  'Food and Beverages',
  'Home Decor',
  'Others',
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
  } catch (error) {

    //go to error page
    router.push('/error-unauthorized');

    console.error(error);
  }
};

const allItemLoad = debounce(() => {
  let requestURL='/api/items/get-all-items?per_page='+rowPerPageAllItems.value+'&page='+currentPageAllItems.value;
  if(typeSearch.value && typeSearch.value.length > 0){
    requestURL += '&type='+typeSearch.value;
  }
  if(conditionSearch.value){
    requestURL += '&condition='+conditionSearch.value;
  }
  if(searchValueAllItems.value && searchValueAllItems.value.length > 2){
    requestURL += '&search='+searchValueAllItems.value;
  }
  if(sortItem.value){
    if(sortItem.value === 'Price:Low to High'){
      requestURL += '&sort=price&order=asc';
    }
    else if(sortItem.value === 'Price:High to Low'){
      requestURL += '&sort=price&order=desc';
    }
    else if(sortItem.value === 'name: A to Z'){
      requestURL += '&sort=name&order=asc';
    }
    else if(sortItem.value === 'name: Z to A'){
      requestURL += '&sort=name&order=desc';
    }
  }
  if(sliderValues.value[0] !== 0 || sliderValues.value[1] !== 200){
    requestURL += '&min_price='+sliderValues.value[0]+'&max_price='+sliderValues.value[1];
  }
  axios.get(requestURL).then(({data}) => {
    totalPagesAllItems.value = Math.ceil(data.data.total / rowPerPageAllItems.value);
    allItems.value = data.data.data;

    allItems.value.forEach((item) => {
      item.pictures.forEach((picture) => {
        picture.picture_path = 'http://127.0.0.1:8000/storage/' + picture.picture_path;
      });
    });

    // if the user_id is the same as the user logged in, then the save the item_id
    allItems.value.forEach((item) => {
      if(item.user_id === user.value.user_id){
        item.isOwn = true;
      }
      else{
        item.isOwn = false;
      }
    });

    // make the created_at date more readable, remove seconds, make it am/pm
    allItems.value.forEach((item) => {
      item.created_at = new Date(item.created_at).toLocaleString('en-US', {
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
}, 1000);


const itemsLoad = debounce(() => {
  let requestURL='/api/items/get-auth-items?per_page='+rowPerPage.value+'&page='+currentPage.value;
  if(searchValue.value && searchValue.value.length > 2){
    requestURL += '&search='+searchValue.value;
  }
  axios.get(requestURL).then(({data}) => {
    
    totalPages.value = Math.ceil(data.data.total / rowPerPage.value);
    items.value = data.data.data;
    items.value.forEach((item) => {
      item.pictures.forEach((picture) => {
        picture.picture_path = 'http://127.0.0.1:8000/storage/' + picture.picture_path;
      });
    });

  }).catch((error) => {
    console.log(error);
  });
}, 1000);


const createItem = () => {
  axios.post('/api/items/store', {
    user_id: user.value.user_id,
    name: name.value,
    description: description.value,
    price: price.value,
    condition: condition.value,
    type: type.value,
    quantity: quantity.value,
    images: images.value,
    availability: 'Available',
  }).then((response) => {
    name.value = '';
    description.value = '';
    price.value = '';
    condition.value = '';
    type.value = '';
    addNewDialog.value = false;
    images.value = [];
    addPictureBox.value = false;
    isAddAlert.value = true;
    itemsLoad();
    allItemLoad();

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

// Watch for changes in selling items
watch([rowPerPage, currentPage, searchValue], () => {
  itemsLoad();
});

// Watch for changes in all items
watch([rowPerPageAllItems, currentPageAllItems, typeSearch, conditionSearch, searchValueAllItems, sortItem], () => {
  allItemLoad();
});

watch(sliderValues, debounce((newValue) => {
  allItemLoad();
}, 1500), { immediate: true, deep: true });



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

itemsLoad();
getUser();
allItemLoad();

</script>

<template>

<div class="box-style">
  <h2 class="mt-3 ml-3 text-overline-4" style="font-weight: 400;">Filter Items</h2>
  <VRow class="mb-2 mt-2 ml-2 mr-2">
    <VCol cols="12" md="3">
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
    
    <VCol cols="12" md="3">
      <VCombobox
        v-model="conditionSearch"
        :items="conditions"
        prepend-inner-icon="ri-thumb-up-line"
        item-title="name"
        placeholder="Select Condition"
        return-object
        label="Condition"
        clearable
      />
    </VCol>
    <VCol cols="12" md="3">
        <VRangeSlider
          v-model="sliderValues"
          :min="0"
          :max="800"
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
          <p class="text-center mt-2" @click="sliderValues = [0, 200]">
            <VTooltip
              open-delay="500"
              location="bottom"
              activator="parent"
              transition="scroll-x-transition"
            >
              <span>Click to reset the price range</span>
            </VTooltip>
            Price range: RM{{ sliderValues[0] }} - RM{{ sliderValues[1] }}
          </p>

      </VCol>
    <VCol cols="12" md="3" >
      <VTextField
        v-model="searchValueAllItems"
        placeholder="Search"
        label="Search name or description of item"
        clearable
        dense
      />
    </VCol>
  </VRow>


</div>

  <div class="box-style">
  <!-- Item List -->
  <VRow class="mt-2 mr-2 mb-1 ml-2">
    <VCol cols="12" md="4" >
      <VBtn @click="ownSellingItems=true">
        <VTooltip
          location="top"
          activator="parent"
          transition="scroll-x-transition"
        >
          <span>Click to view your selling items</span>
      </VTooltip>
        Own Selling Items
      </VBtn>
    </VCol>
    <VCol cols="12" md="4"/>
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
            v-for="item in sortBy"
            :key="item.title"
            @click="sortItem = item.value"
          >
            <VListItemTitle>{{ item.title }}</VListItemTitle>
          </VListItem>
        </VList>
      </VMenu>
    </VCol>
  </VRow>
    <div class="item-list-container">
      <ItemList 
        :allItems="allItems"
        :allItemLoad="allItemLoad"
      />
    </div>
    <VCardText>
      <VRow>
        <VCol cols="12" md="4" />
        <VCol cols="12" md="4">
          <VPagination
            v-model="currentPageAllItems"
            variant="outlined"
            :length="totalPagesAllItems"
            rounded="circle"
          />
        </VCol>
        <VCol cols="12" md="4" />
      </VRow>
    </VCardText>
  </div>

  <VDialog
    v-model="ownSellingItems"
    max-width="1200"
  >

  <div class="box-style">

  <!-- 'Own Selling Item Table -->
  <VRow>
    <VCol cols="12" md="3">
      <div class="mt-5 ml-5 mb-4">
        <VBtn @click="addPictureBox=true">
          Sell New Item
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
        label="Search name of the item"
        clearable
        dense
      />
    </VCol>
  </VRow>

  <div class="table-style">
    <TableItem
      :items="items"
      :itemsLoad="itemsLoad"
      :allItemLoad="allItemLoad"
      :conditions="conditions"
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



  <!-- Add New Item Dialog -->
<VDialog
    v-model="addNewDialog"
    max-width="800"
  >
    <!-- Dialog Content -->
    <VCard title="Sell New Item">

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
                v-model="condition"
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
                <label for="firstNameHorizontalIcons">Quantity</label>
              </VCol>

              <VCol
                cols="12"
                md="9"
              >
                <VTextField
                  v-model="quantity"
                  prepend-inner-icon="ri-shopping-bag-4-line"
                  
                  placeholder="1"
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
                  v-model="price"
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
              @click="$refs.refForm.validate().then(() => createItem())"
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
          <div v-if="images.length > 0" class="uploaded-images">
            <p class="upload-text">Selected images: </p>
          <div v-for="(image, index) in images" :key="index" class="uploaded-image">
            <img :src="image.url" alt="Uploaded Image" />
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
      New item has been successfully registered.
    </VSnackbar>

    <!--Snackbar-->
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


