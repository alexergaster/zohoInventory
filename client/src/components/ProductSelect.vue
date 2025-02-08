<template>
  <div class="mb-4">
    <label class="block text-red-600 text-sm font-bold mb-2">Товари*</label>
    <div class="relative">
      <select
        class="w-full p-3 border rounded-md shadow-sm"
        v-model="selected"
        @change="$emit('add')"
      >
        <option
          v-for="product in products"
          :key="product.item_id"
          :value="product"
        >
          {{ product.name }} - {{ product.available_stock }} шт.
        </option>
      </select>
      <button
        class="absolute right-6 top-1/2 transform -translate-y-1/2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded cursor-pointer"
      >
        <i class=""> + </i>
      </button>
    </div>
  </div>
</template>

<script setup>
import { defineEmits, defineProps, onMounted, ref } from "vue";
import { getItems } from "../api";

const products = ref([]);
const emit = defineEmits(["update:modelValue", "add"]);

const selected = defineModel();

onMounted(() => {
  getItems().then((data) => {
    products.value = data.data.items.filter(
      (product) => product.status !== "inactive"
    );
    console.log("Products", products.value);
  });
});
</script>
