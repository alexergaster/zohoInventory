<template>
  <div class="mb-4">
    <label :for="id" class="block text-gray-700 text-sm mb-2">
      {{ label }}
    </label>
    <input
      :type="type"
      :id="id"
      :value="modelValue"
      @input="emit('update:modelValue', $event.target.value)"
      class="w-full p-3 border rounded-md shadow-sm focus:outline-none focus:ring focus:ring-blue-300"
    />
    <p v-if="error" class="text-red-500 text-xs mt-1">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref, watch } from "vue";

const props = defineProps({
  modelValue: [String, Number],
  type: {
    type: String,
    default: "text",
  },
  label: String,
  id: String,
  rules: {
    type: Array,
    default: () => [],
  },
});

const emit = defineEmits(["update:modelValue"]);

const error = ref("");

const validate = () => {
  for (let rule of props.rules) {
    const validationError = rule(props.modelValue);
    if (validationError) {
      error.value = validationError;
      return;
    }
  }
  error.value = "";
};

watch(() => props.modelValue, validate);
</script>
