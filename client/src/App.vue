<template>
  <div class="bg-white shadow-md rounded-md px-6 py-8">
    <h2 class="text-2xl font-bold mb-6">New Sales Order</h2>

    <CustomerSelect v-model="selectContact" />

    <DatePickerInput v-model="date" />

    <ProductSelect v-model="selectProduct" @add="addProduct" />

    <ProductTable
      v-if="addedProducts.length"
      v-model="addedProducts"
      :products="addedProducts"
      @remove="removeProduct"
    />

    <div class="mt-4">
      <label for="purchases" class="flex items-center space-x-2 cursor-pointer">
        <input
          type="checkbox"
          id="purchases"
          class="peer hidden"
          :disabled="isPurchaseDisabled"
          v-model="purchaseOrder"
        />

        <div
          :disabled="isPurchaseDisabled"
          class="w-5 h-5 border-2 peer-disabled:bg-gray-400 border-gray-400 rounded-md flex items-center justify-center peer-checked:bg-green-500 peer-checked:border-green-500 transition duration-200"
        >
          <svg
            v-if="purchaseOrder"
            class="w-4 h-4 text-white"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M5 13l4 4L19 7"
            ></path>
          </svg>
        </div>
        <span class="text-sm font-bold text-gray-600">
          Створити закупівлю у постачальника
        </span>
      </label>
    </div>

    <div class="mt-4 flex justify-end">
      <button
        :disabled="!addedProducts.length"
        @click="salesOrder"
        class="bg-green-500 cursor-pointer transition-all duration-300 hover:bg-green-700 text-white font-bold py-2 px-4 rounded disabled:bg-gray-300"
      >
        Замовити
      </button>
    </div>
  </div>
</template>

<script setup>
import CustomerSelect from "./components/CustomerSelect.vue";
import ProductSelect from "./components/ProductSelect.vue";
import ProductTable from "./components/ProductTable.vue";
import DatePickerInput from "./components/DatePickerInput.vue";

import { ref, watch, computed } from "vue";
import { salesOrders } from "./api";

const date = ref(null);

const addedProducts = ref([]);
const selectContact = ref({});
const selectProduct = ref({});

let isPurchaseDisabled = ref(true);
const purchaseOrder = ref(false);

const removeProduct = (id) => {
  addedProducts.value = addedProducts.value.filter(
    (product) => product.item_id !== id
  );

  isPurchaseDisabled = !computePurchase.value;

  selectProduct.value = {};
};

const addProduct = () => {
  if (addedProducts.value.includes(selectProduct.value)) return;

  selectProduct.value.quantity = 1;
  addedProducts.value.push(selectProduct.value);
};

const salesOrder = () => {
  const data = {
    customer_id: selectContact.value.contact_id,
    date: date.value ?? "",
    line_items: addedProducts.value.map((product) => ({
      item_id: product.item_id,
      quantity: product.quantity,
    })),
    is_purchase_order: purchaseOrder.value,
  };

  console.log("Data", data);

  // salesOrders(data).then((data) => {
  //   console.log("Sales Order", data);
  // });
};

const computePurchase = computed(() => {
  return addedProducts.value.some(
    (product) =>
      product.available_stock && product.quantity < product.available_stock
  );
});

watch(
  addedProducts.value,
  () => {
    console.log("Added Products", addedProducts.value);

    isPurchaseDisabled.value = computePurchase.value;
  },
  { deep: true }
);
</script>
