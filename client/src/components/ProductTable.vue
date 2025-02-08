<template>
  <div class="overflow-x-auto">
    <table
      class="min-w-full bg-white border border-gray-300 shadow-md rounded-lg"
    >
      <thead>
        <tr class="bg-gray-100">
          <th class="px-4 py-2 text-left">Назва товару</th>
          <th class="px-4 py-2 text-center">Кількість</th>
          <th class="px-4 py-2 text-center">Ціна</th>
          <th class="px-4 py-2 text-center">Сума</th>
          <th class="px-4 py-2 text-center">Дія</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="product in products" :key="product.item_id" class="border-t">
          <td class="px-4 py-2">{{ product.name }}</td>
          <td class="px-4 py-2 text-center">
            <input
              type="number"
              v-model.number="product.quantity"
              min="1"
              class="w-16 p-1 border rounded text-center"
            />
          </td>
          <td class="px-4 py-2 text-center">{{ product.rate }} грн</td>
          <td class="px-4 py-2 text-center">
            {{ calculateTotalProduct(product) }} грн
          </td>
          <td class="px-4 py-2 text-center">
            <button
              @click="$emit('remove', product.item_id)"
              class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-700"
            >
              Видалити
            </button>
          </td>
        </tr>
      </tbody>
      <tfoot>
        <tr class="bg-gray-100 font-bold">
          <td colspan="4" class="px-4 py-2 text-right">Загальна сума:</td>
          <td class="px-4 py-2 text-center">{{ calculateTotal }} грн</td>
          <td></td>
        </tr>
      </tfoot>
    </table>
  </div>
</template>

<script setup>
import { defineEmits, defineProps, computed } from "vue";

const props = defineProps(["products"]);
const emit = defineEmits(["remove"]);
const calculateTotalProduct = (product) => product.quantity * product.rate;

const calculateTotal = computed(() => {
  return props.products.reduce(
    (acc, product) => acc + product.quantity * product.rate,
    0
  );
});
</script>
