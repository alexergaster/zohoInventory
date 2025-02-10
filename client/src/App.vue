<template>
  <div class="bg-white shadow-md rounded-md px-6 py-8">
    <h2 class="text-2xl font-bold mb-6">New Sales Order</h2>

    <CustomerSelect @update:contact="contact = $event" />

    <DatePickerInput v-model="date" />

    <ProductTable v-model="products" @update:products="products = $event" />

    <PurchaseCheckbox
      :product="products"
      @update:purchaseOrder="purchaseOrder = $event"
    />
    <div
      v-if="message?.type"
      class="my-4 p-2 rounded border-2 text-center"
      :class="{
        'border-red-600': message.type === 'error',
        'border-green-600': message.type === 'success',
      }"
    >
      <p
        class="block text-sm"
        :class="{
          'text-red-600': message.type === 'error',
          'text-green-600': message.type === 'success',
        }"
      >
        {{ message.text }}
      </p>
    </div>

    <div class="mt-4 flex justify-end">
      <button
        :disabled="!isButtonEnabled"
        @click="salesOrder"
        class="bg-green-500 cursor-pointer transition-all duration-300 hover:bg-green-700 text-white font-bold py-2 px-4 rounded disabled:bg-gray-300"
      >
        Замовити
      </button>
    </div>
  </div>
</template>

<script setup>
import CustomerSelect from "./components/UI/CustomerSelect.vue";
import DatePickerInput from "./components/UI/DatePickerInput.vue";
import ProductSelect from "./components/UI/ProductSelect.vue";
import ProductTable from "./components/UI/ProductTable.vue";
import PurchaseCheckbox from "./components/UI/PurchaseCheckbox.vue";

import { ref, computed, watch } from "vue";
import { salesOrders } from "./api";

const date = ref(null);
const products = ref([]);
const contact = ref({});
const purchaseOrder = ref(false);
const message = ref({});

const salesOrder = () => {
  message.value = {};

  const data = {
    customer_id: contact.value.contact_id,
    date: date.value ?? "",
    line_items: products.value.map((products) => ({
      item_id: products.item_id,
      quantity: products.quantity,
      tax_id: products.tax,
    })),
    is_purchase_order: purchaseOrder.value,
  };

  console.log(data);

  salesOrders(data).then((data) => {
    if (data.data.code === 0) {
      message.value = {
        type: "success",
        text: "Замовлення створенно успішно!",
      };
    } else {
      message.value = { type: "error", text: data.data.message };
    }
  });
};

const isButtonEnabled = computed(() => {
  return products.value.length > 0 && Boolean(contact.value.contact_id);
});

watch(
  [contact, products, date, purchaseOrder],
  () => {
    message.value = {};
  },
  { deep: true }
);
</script>
