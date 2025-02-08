<template>
  <div class="relative mb-4">
    <label for="date" class="block text-gray-700 text-sm font-bold mb-2">
      Дата
    </label>
    <input
      type="text"
      id="date"
      class="w-full p-3 border rounded-md shadow-sm focus:outline-none focus:ring focus:ring-blue-300"
      :value="selectedDate"
      @focus="showDatePicker = true"
      readonly
    />
    <Datepicker
      v-if="showDatePicker"
      v-model="selectedDate"
      @update:modelValue="updateDate"
      :enable-time-picker="false"
      :inline="true"
      :first-day-of-week="1"
      class="absolute z-10 bg-white shadow-md rounded-md"
    />
  </div>
</template>

<script setup>
import { ref, defineEmits } from "vue";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";

const emit = defineEmits(["update:modelValue"]);
const selectedDate = ref("");
const showDatePicker = ref(false);

const updateDate = (value) => {
  selectedDate.value = formatDate(value);
  emit("update:modelValue", selectedDate.value);
  showDatePicker.value = false;
};

const formatDate = (date) => {
  return date ? new Intl.DateTimeFormat("uk-UA").format(date) : "";
};
</script>
