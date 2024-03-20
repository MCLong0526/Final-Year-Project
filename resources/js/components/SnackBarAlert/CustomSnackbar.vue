<template>
  <VSnackbar
    v-model="showSnackbar"
    :color="getColor(type)"
    location="top end"
    transition="scale-transition"
  >
    <VIcon size="20" class="me-2">{{ getIcon(type) }}</VIcon>
    {{ message }}
  </VSnackbar>
</template>

<script setup>
import { ref } from 'vue';

const showSnackbar = ref(false);
const message = ref('');
const type = 'success';

function getColor(type) {
  return type === 'error' ? 'error' : 'success';
}

function getIcon(type) {
  return type === 'error' ? 'ri-error-warning-line' : 'ri-checkbox-circle-line';
}

// Watch for changes in the message prop and show the snackbar
watch(() => props.message, (newValue) => {
  if (newValue) {
    message.value = newValue;
    showSnackbar.value = true;
  }
});
</script>
