<script setup>
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

});

const deleteDialog = ref(false);
const clickedService = ref(null);
const isDeleteAlert = ref(false);

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
          RM {{ service.price_per_hour }}
        </td>
        <td class="text-center">
          <VChip v-if="service.availability=='Available'" color="success">
            {{ service.availability }}
          </VChip>
          <VChip v-else color="error">
            {{ service.availability }}
          </VChip>
          
        </td>
        <td class="text-center">
          <VBtn
            color="info"
            style="margin-inline: 15px 3px"
            size="small"
            
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

  <VSnackbar
      v-model="isDeleteAlert"
      location="top end"
      transition="scale-transition"
      color="success"
    >
    <VIcon size="20" class="me-2">ri-checkbox-circle-line</VIcon>
      Service <strong>{{ clickedService.name }}</strong> has been successfully deleted.
    </VSnackbar>
</template>
