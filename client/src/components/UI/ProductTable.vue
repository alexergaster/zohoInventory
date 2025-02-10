<template>
  <div class="overflow-x-auto">
    <table
      class="min-w-full bg-white border border-gray-300 shadow-md rounded-lg"
    >
      <thead>
        <tr class="bg-gray-100">
          <th class="px-4 py-2 text-left text-red-600">Товар*</th>
          <th class="px-4 py-2 text-center">Кількість</th>
          <th class="px-4 py-2 text-center">Ціна</th>
          <th class="px-4 py-2 text-center">Податок</th>
          <th class="px-4 py-2 text-center">Сума</th>
          <th class="px-4 py-2 text-center">Дія</th>
        </tr>
      </thead>
      <tbody class="px-4 py-2">
        <tr
          v-for="(product, index) in products"
          :key="product.item_id"
          class="border-t"
        >
          <td>
            <ProductSelect
              class="px-4 py-2"
              :products="allProducts"
              :selected-product="selectProduct[index]"
              @update:product="updateProduct(index, $event)"
            />
          </td>
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
            <select
              class="w-full p-3 border rounded-md shadow-sm"
              v-model="product.tax"
            >
              <option v-for="tax in taxOptions" :key="tax.id" :value="tax.id">
                {{ tax.value }}%
              </option>
            </select>
          </td>
          <td class="px-4 py-2 text-center">
            {{ calculateTotalProduct(product) }} грн
          </td>
          <td class="px-4 py-2 text-center">
            <button
              @click="removeProduct(index, product.item_id)"
              class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-700"
            >
              Видалити
            </button>
          </td>
        </tr>
        <button
          class="m-4 bg-gray-300 text-blue-500 hover:bg-gray-500 transition-all duration-300 cursor-pointer hover:text-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-300 rounded px-4 py-2"
          @click="addProduct"
        >
          Додати товар
        </button>
      </tbody>
      <tfoot>
        <tr class="bg-gray-100 font-bold">
          <td colspan="5" class="px-4 py-2 text-right">
            Загальна сума: {{ calculateTotal }} грн
          </td>
          <td class="px-4 py-2 text-right">
            Податки: {{ calculateTotalTax }} грн
          </td>
        </tr>
      </tfoot>
    </table>
  </div>
</template>

<script setup>
import ProductSelect from "./ProductSelect.vue";
import { getItems } from "../../api";

import { defineEmits, defineProps, onMounted, computed, ref } from "vue";

const taxOptions = ref([
  { id: "701659000000078001", value: 1 },
  { id: "701659000000078007", value: 50 },
]);
const products = ref([{ rate: 0, quantity: 0, tax: 0 }]);
const allProducts = ref([]);
const emit = defineEmits(["update:products"]);
const selectProduct = ref([]);

const updateProduct = (index, selectedProduct) => {
  if (
    allProducts.value.some(
      (product) => product.item_id !== selectedProduct.item_id
    )
  ) {
    allProducts.value.push(selectedProduct);
  }

  selectProduct.value[index] = selectedProduct;

  products.value[index] = { ...selectedProduct, quantity: 1, tax: 0 };

  emit("update:products", products.value);
};

const calculateTotalProduct = (product) => product.quantity * product.rate;

const removeProduct = (index, id) => {
  selectProduct.value[index] = null;

  products.value = products.value.filter((product) => product.item_id !== id);

  emit("update:products", products.value);
};

const taxValue = (taxId) => {
  const tax = taxOptions.value.find((tax) => tax.id === taxId);
  return tax ? tax.value : null;
};

const calculateTotal = computed(() => {
  return products.value.reduce(
    (acc, product) =>
      Math.floor(
        acc +
          product.quantity * product.rate * (1 + taxValue(product.tax) / 100)
      ),
    0
  );
});

const calculateTotalTax = computed(() => {
  return products.value.reduce(
    (acc, product) =>
      Math.floor(
        acc + product.quantity * product.rate * (taxValue(product.tax) / 100)
      ),
    0
  );
});

const addProduct = () => {
  products.value.push({ rate: 0, quantity: 0, tax: 0 });
};

onMounted(() => {
  getItems().then((data) => {
    console.log("Products", data);

    allProducts.value = data.data.items.filter(
      (product) => product.status !== "inactive"
    );
  });
});
</script>
