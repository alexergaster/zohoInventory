<template>
  <div class="bg-white shadow-md rounded-md px-6 py-8">
    <h2 class="text-2xl font-bold mb-6">New Sales Order</h2>

    <div class="mb-4">
      <label
        for="contactName"
        class="block text-red-600 text-sm font-bold mb-2"
      >
        Обрати клієнта*
      </label>
      <div class="relative">
        <select
          id="contactName"
          class="w-full p-3 border rounded-md shadow-sm focus:outline-none focus:ring focus:ring-blue-300"
          v-model="selectContact"
        >
          <option
            v-for="contact in contacts"
            :key="contact.contact_id"
            :value="contact"
          >
            {{ contact.contact_name }}
          </option>
        </select>
        <button
          @click="isModalOpen = true"
          class="absolute right-6 top-1/2 transform -translate-y-1/2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded cursor-pointer"
        >
          <i> + </i>
        </button>
      </div>
    </div>

    <div class="mb-4">
      <label for="date" class="block text-gray-700 text-sm font-bold mb-2">
        Дата
      </label>
      <input
        type="text"
        id="date"
        class="w-full p-3 border rounded-md shadow-sm focus:outline-none focus:ring focus:ring-blue-300"
        :value="selectedDate ? formatDate(selectedDate) : ''"
        @focus="showDatePicker = true"
        readonly
      />
      <Datepicker
        v-if="showDatePicker"
        v-model="selectedDate"
        @update:modelValue="showDatePicker = false"
        :enable-time-picker="false"
        :inline="true"
        :first-day-of-week="1"
        class="absolute z-10 bg-white shadow-md rounded-md"
      />
    </div>

    <div class="mb-4">
      <label for="product" class="block text-red-600 text-sm font-bold mb-2">
        Товари*
      </label>
      <div class="relative">
        <select
          id="product"
          class="w-full p-3 border rounded-md shadow-sm focus:outline-none focus:ring focus:ring-blue-300"
          v-model="selectProduct"
          @change="addProduct"
        >
          <option
            v-for="product in products"
            :key="product.item_id"
            :value="product"
          >
            {{ product.name }}
          </option>
        </select>
        <button
          class="absolute right-6 top-1/2 transform -translate-y-1/2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded cursor-pointer"
        >
          <i class=""> + </i>
        </button>
      </div>
    </div>

    <div class="overflow-x-auto" v-if="addedProducts.length">
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
          <tr
            v-for="product in addedProducts"
            :key="product.item_id"
            class="border-t"
          >
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
                @click="removeProduct(product.item_id)"
                class="bg-red-500 text-white px-3 py-1 rounded transition-all duration-300 hover:bg-red-700"
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

    <ModalItem
      :isModalOpen="isModalOpen"
      @close="isModalOpen = false"
      @confirm="handleConfirm"
    >
      <template #title>Додати клієнта</template>
      <template #content>
        <div class="mb-4">
          <label for="name" class="block text-red-700 text-sm font-bold mb-2">
            Ім'я клієнта*
          </label>
          <input
            type="text"
            id="name"
            v-model="contactData.name"
            class="w-full p-3 border rounded-md shadow-sm focus:outline-none focus:ring focus:ring-blue-300"
          />
        </div>
        <div class="mb-4">
          <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
            Email
          </label>
          <input
            v-model="contactData.email"
            type="email"
            id="email"
            class="w-full p-3 border rounded-md shadow-sm focus:outline-none focus:ring focus:ring-blue-300"
          />
        </div>

        <div class="mb-4 p-2 border-red-600 rounded border-2 text-center">
          <p class="block text-red-600 text-sm"></p>
        </div>
      </template>
    </ModalItem>
  </div>
</template>

<script setup>
import ModalItem from "./components/ModalItem.vue";

import { ref, onMounted, watch, computed } from "vue";
import { getContacts, getItems, addContact } from "./api";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";

const selectedDate = ref(null);
const showDatePicker = ref(false);
const formatDate = (date) => {
  if (!date) return "";
  const options = { day: "2-digit", month: "numeric", year: "numeric" };
  return new Intl.DateTimeFormat("ua-UA", options).format(date);
};

const contactData = ref({
  name: "",
  email: "",
});
const isModalOpen = ref(false);
const handleConfirm = () => {
  const body = {
    contact_name: contactData.value.name,
    email: contactData.value.email,
  };

  addContact(body).then((data) => {
    contacts.value.push(data.data.contact);
    selectContact.value = data.data.contact;
    isModalOpen.value = false;
  });
};

const selectContact = ref({});
const selectProduct = ref({});

let isPurchaseDisabled = ref(true);
const contact = ref({
  name: "",
  email: "",
});
const product = ref({
  id: "",
  quantity: "",
});
const purchaseOrder = ref(false);

const contacts = ref([]);
const products = ref([]);

const addedProducts = ref([]);

const removeProduct = (id) => {
  addedProducts.value = addedProducts.value.filter(
    (product) => product.item_id !== id
  );

  isPurchaseDisabled = !computePurchase.value;

  selectProduct.value = {};
};
const calculateTotalProduct = (product) => {
  return product.quantity * product.rate;
};
const calculateTotal = computed(() => {
  return addedProducts.value.reduce(
    (acc, product) => acc + product.quantity * product.rate,
    0
  );
});
const addProduct = () => {
  if (addedProducts.value.includes(selectProduct.value)) return;

  selectProduct.value.quantity = 1;
  addedProducts.value.push(selectProduct.value);
};

const salesOrder = () => {
  console.log("Sales Order");
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

    isPurchaseDisabled = computePurchase.value;
  },
  { deep: true }
);

onMounted(() => {
  getContacts().then((data) => {
    contacts.value = data.data.contacts.filter(
      (contact) => contact.contact_type_formatted === "Customer"
    );
    console.log("Contacts", contacts.value);
  });

  getItems().then((data) => {
    products.value = data.data.items.filter(
      (product) => product.status !== "inactive"
    );
    console.log("Products", products.value);
  });
});
</script>

<style>
.modal_bg {
  background-color: rgba(0, 0, 0, 0.5);
}
</style>
