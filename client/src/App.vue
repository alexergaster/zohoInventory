<template>
  <div class="bg-white shadow-md rounded-md px-6 py-8">
    <h2 class="text-2xl font-bold mb-6">New Sales Order</h2>

    <CustomerSelect v-model="contact" />

    <DatePickerInput v-model="date" />

    <ProductSelect @update:products="product.push($event)" />

    <ProductTable
      v-if="product.length"
      v-model="product"
      :products="product"
      @remove-products="product = $event"
    />

    <PurchaseCheckbox
      :product="product"
      @update:purchaseOrder="purchaseOrder = $event"
    />

    <div class="mt-4 flex justify-end">
      <button
        :disabled="!product.length"
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
import DatePickerInput from "./components/DatePickerInput.vue";
import ProductSelect from "./components/ProductSelect.vue";
import ProductTable from "./components/ProductTable.vue";
import PurchaseCheckbox from "./components/PurchaseCheckbox.vue";

import { ref, watch, computed } from "vue";
import { salesOrders } from "./api";

const date = ref(null);

const product = ref([]);
const contact = ref({});
const purchaseOrder = ref(false);

const salesOrder = () => {
  const data = {
    customer_id: contact.value.contact_id,
    date: date.value ?? "",
    line_items: product.value.map((product) => ({
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
</script>
