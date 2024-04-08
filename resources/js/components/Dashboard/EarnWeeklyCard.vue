<script setup>

const props = defineProps({
 number_of_approved: {
   type: Number,
   required: true,
 },
  number_of_pending: {
    type: Number,
    required: true,
  },
  number_of_rejected: {
    type: Number,
    required: true,
  },
  total_earned: {
    type: Number,
    required: true,
  },

})

const status = [
  {
    title: 'Approved',
    amount: props.number_of_approved,
  },
  {
    title: 'Pending',
    amount: props.number_of_pending,
  },
  {
    title: 'Rejected',
    amount: props.number_of_rejected,
  },
]


const resolveDotColor = title => {
  if (title === 'Approved')
    return 'success'
  else if (title === 'Pending')
    return 'warning'
  else
    return 'error'
}
</script>

<template>
  <VCard title="Approximate Earnings" subtitle="Through Provide Services">

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
            icon="ri-bank-line"
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
          <tr
            v-for="sta in status"
            :key="sta.title"
          >
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
              <VChip :color="resolveDotColor(sta.title)">{{ sta.amount }}</VChip> 
            </td>


          </tr>
        </tbody>
      </VTable>
    </VCardText>
  </VCard>
</template>
