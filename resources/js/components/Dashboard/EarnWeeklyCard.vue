<script setup>
import { reactive, ref } from 'vue';

const props = defineProps({
  number_of_approved_service: {
    type: Number,
    required: true,
  },
  number_of_pending_service: {
    type: Number,
    required: true,
  },
  number_of_rejected_service: {
    type: Number,
    required: true,
  },
  number_of_cancelled_service: {
    type: Number,
    required: true,
  },
  total_earned_service: {
    type: Number,
    required: true,
  },
  total_earned_item: {
    type: Number,
    required: true,
  },
  number_of_approved_item: {
    type: Number,
    required: true,
  },
  number_of_pending_item: {
    type: Number,
    required: true,
  },
  number_of_rejected_item: {
    type: Number,
    required: true,
  },
  number_of_cancelled_item: {
    type: Number,
    required: true,
  },
  
});

const status = reactive([
  {
    title: 'Approved',
    amount: props.number_of_approved_service + props.number_of_approved_item,
  },
  {
    title: 'Pending',
    amount: props.number_of_pending_service + props.number_of_pending_item,
  },
  {
    title: 'Rejected',
    amount: props.number_of_rejected_service + props.number_of_rejected_item,
  },
  {
    title: 'Cancelled',
    amount: props.number_of_cancelled_service + props.number_of_cancelled_item,
  },
]);

const resolveDotColor = (title) => {
  if (title === 'Approved') return 'success';
  if (title === 'Pending') return 'warning';
  return 'error';
};

const total_earned = ref(
  props.total_earned_service + props.total_earned_item
);

const currentType = ref('both');

const filterTo = (type) => {
  if (type === 'services') {
    total_earned.value = props.total_earned_service;
    status[0].amount = props.number_of_approved_service;
    status[1].amount = props.number_of_pending_service;
    status[2].amount = props.number_of_rejected_service;
    status[3].amount = props.number_of_cancelled_service;
    currentType.value = 'services';
  } else if (type === 'items') {
    total_earned.value = props.total_earned_item;
    status[0].amount = props.number_of_approved_item;
    status[1].amount = props.number_of_pending_item;
    status[2].amount = props.number_of_rejected_item;
    status[3].amount = props.number_of_cancelled_item;
    currentType.value = 'items';
  } else {
    total_earned.value = props.total_earned_service + props.total_earned_item;
    status[0].amount = props.number_of_approved_service + props.number_of_approved_item;
    status[1].amount = props.number_of_pending_service + props.number_of_pending_item;
    status[2].amount = props.number_of_rejected_service + props.number_of_rejected_item;
    status[3].amount = props.number_of_cancelled_service + props.number_of_cancelled_item;
    currentType.value = 'both';
  }
};

</script>

<template>
  <VCard title="Approximate Earnings">
    <VCardSubtitle v-if="currentType === 'services'" class="ml-1">
      Through Provide Services
    </VCardSubtitle>
    <VCardSubtitle v-else-if="currentType === 'items'" class="ml-1">
      Through Selling Items
    </VCardSubtitle>
    <VCardSubtitle v-else class="ml-1">
      Through Provide Services and Selling Items
    </VCardSubtitle>
    <template #append>
      <MoreBtn class="me-n3 mt-n1" i/>
      <VMenu
        activator="parent"
        width="240"
        location="end"
        offset="14px"
      >
        <VList>
          <VListItem @click="filterTo('services')">
            
            Provide Services
          </VListItem>
          <VListItem @click="filterTo('items')">
              
              Selling Items
          </VListItem>
    
          <VListItem @click="filterTo('both')">
            Both
          </VListItem>
        </VList>
      </VMenu>
  </template>
    <VCardText>
      <div class="d-flex align-center mb-6">
        <VAvatar
          rounded
          variant="tonal"
          color="primary"
          size="50"
          class="me-4"
        >
          <VIcon
            icon="ri-wallet-3-line"
            size="32"
          />
        </VAvatar>
        <div style="line-height: 1;">
          <h4 class="text-h4">
            RM {{ total_earned }}
          </h4>
          <span class="text-xs">Total Earned</span>
        </div>
      </div>
      <VTable class="text-high-emphasis text-no-wrap">
        <tbody>
          <tr v-for="sta in status" :key="sta.title">
            <td class="ps-0">
              <div class="d-flex align-center gap-2">
                <VIcon
                  icon="ri-circle-fill"
                  :color="resolveDotColor(sta.title)"
                  :size="16"
                />
                {{ sta.title }}
              </div>
            </td>
            <td class="font-weight-semibold text-end">
              <VChip :color="resolveDotColor(sta.title)">
                {{ sta.amount }}
              </VChip>
            </td>
          </tr>
        </tbody>
      </VTable>
    </VCardText>
  </VCard>
</template>
