<template>
  <div
    v-if="isModalOpen"
    class="fixed inset-0 flex items-center justify-center z-50 modal_bg"
    @click.self="closeModal"
  >
    <div
      class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md relative transition"
    >
      <h2 class="text-xl font-semibold mb-4">
        <slot name="title"></slot>
      </h2>

      <slot name="content"></slot>

      <div class="mt-4 flex justify-end space-x-2">
        <button
          @click="closeModal"
          class="bg-gray-400 text-white px-4 py-2 rounded-md hover:bg-gray-500 transition"
        >
          Закрити
        </button>
        <button
          class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition"
          @click="confirmAction"
        >
          Підтвердити
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from "vue";

const emit = defineEmits(["close", "confirm"]);

const closeModal = () => {
  emit("close");
};
const confirmAction = () => {
  emit("confirm");
};

defineProps({
  isModalOpen: {
    type: Boolean,
    required: true,
    default: false,
  },
});
</script>
