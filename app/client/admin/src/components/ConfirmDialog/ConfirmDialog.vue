<script setup lang="ts">
  import { defineProps, defineEmits } from "vue";
  import "./ConfirmDialog.scss"; // Import styles

  defineProps({
    show: Boolean,
    message: String,
  });

  const emit = defineEmits(["confirm", "close"]);
</script>

<template>
  <div v-if="show" class="confirm-modal-backdrop">
    <div class="confirm-modal">
      <p>{{ message }}</p>
      <div class="confirm-modal-actions">
        <button @click="emit('confirm')" class="confirm-confirm">Yes</button>
        <button @click="emit('close')" class="confirm-cancel">Cancel</button>
      </div>
    </div>
  </div>
</template>
