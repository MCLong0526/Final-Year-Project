<script setup>
  
const props = defineProps({
  allItems: {
    type: Object,
    required: true,
  },
  allItemLoad: {
    type: Function,
    required: true,
  },
});

const openItemDialog = ref(false);
const clickedItem = ref({});

const seeItem = (item) => {
  openItemDialog.value = true;
  clickedItem.value = item;
};



</script>

<template>
  <VRow>
    <VCol
      v-for="(item) in allItems.slice(0, 9)"
      :key="item.item_id"
      cols="12"
      md="4"
      sm="6"
    >
      <VCard class="mb-4">
        <VCarousel delimiter-icon="ri-circle-fill" height="250px" hide-delimiter-background show-arrows="hover">
          <VCarouselItem v-for="(image, index) in item.pictures" :key="index" >
            <VImg :src="image.picture_path" height="250px" object-fit="cover" class="mt-2 ml-2 mr-2" />
          </VCarouselItem>
        </VCarousel>
        <VCardTitle>
          <VChip
            color="primary"
            class="mr-2"
          >
          <VIcon icon="ri-price-tag-3-line" class="mr-2"/>
          {{ item.type }}</VChip>
        </VCardTitle>
        <VCardTitle>
          <VIcon icon="ri-money-dollar-circle-line" />

          RM {{item.price}}</VCardTitle>
        <VCardTitle>
          <VIcon icon="ri-shopping-bag-4-line" />
            {{ item.name }}
        </VCardTitle>

        <VCardActions>
          <VBtn @click="seeItem(item)">
            Details
          </VBtn>
        </VCardActions>
      </VCard>
    </VCol>
  </VRow>

  <!--Item Dialog-->
  <VDialog
    v-model="openItemDialog"
    scrollable
    max-width="500"
  >

    <!-- Dialog Content -->
    <VCard>
      <VCardItem class="pb-3">
        <VCardTitle>Item Details</VCardTitle>
      </VCardItem>
      <VCardText style="block-size: 420px;">
        <VCarousel delimiter-icon="ri-circle-fill" height="250px" hide-delimiter-background show-arrows="hover">
          <VCarouselItem v-for="(image, index) in clickedItem.pictures" :key="index" >
            <VImg :src="image.picture_path" height="250px" object-fit="cover" class="mt-2 ml-2 mr-2" />
          </VCarouselItem>
        </VCarousel>
       
        <VCardTitle>
          <VIcon icon="ri-shopping-bag-4-line" />
            {{ clickedItem.name }}
        </VCardTitle>
        <VCardTitle>
          <VIcon icon="ri-money-dollar-circle-line" />

          RM {{clickedItem.price}}
        </VCardTitle>

        <h3 class="mt-2">Seller Information</h3>
        <VCardText class="font-weight-medium text-high-emphasis text-center text-truncate" style="display: flex; align-items: center;">
      
        <VAvatar size="38"
            :color="clickedItem.user.avatar ? '' : 'primary'"
            :class="`${!clickedItem.user.avatar ? 'v-avatar-light-bg primary--text' : ''}`"
            :variant="!clickedItem.user.avatar ? 'tonal' : undefined"
            style="margin-inline-end: 8px;">
            <VImg
            :src="clickedItem.user.avatar"/>
        </VAvatar>
        {{ clickedItem.user.username }}

        </VCardText>  

        <h3 class="mt-2">Details</h3>

        <VCardText>
          <div class="detail-row">
            <strong>Condition</strong>
            <span>: {{ clickedItem.condition }}</span>
          </div>
          <div class="detail-row">
            <strong>Type</strong>
            <span>: {{ clickedItem.type }}</span>
          </div>
        </VCardText>

        <h3 class="mt-2">Description</h3>
        <VCardText>
          {{ clickedItem.description }}
          </VCardText>

      </VCardText>

      <VCardText class="pt-5 text-end">
        <VSpacer />
        <VBtn
          variant="outlined"
          color="secondary"
          class="me-4"
          @click="openItemDialog = false"
        >
          Close
        </VBtn>
        <VBtn @click="openItemDialog = false">
          <VTooltip
            location="top"
            activator="parent"
            transition="scroll-y-transition"
          >
            <span>Interested? </span>
          </VTooltip>
          Check Status
        </VBtn>
      </VCardText>
    </VCard>
  </VDialog>
 
</template>

<style scoped>
.item-details {
  font-size: larger;
}

.detail-row {
  display: flex;
  align-items: center;
  margin-block-end: 8px;
}

.detail-row strong {
  display: inline-block;
  inline-size: 100px; /* Adjust the width as needed */
}

.detail-row span {
  margin-inline-start: 8px;
}
</style>

