<template>
  <!-- <div class="max-w-lg mx-auto bg-gray-800 p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">Форма замовлення</h2>

    <form id="orderForm" @submit.prevent="sendForm">
      <div class="mb-4">
        <label for="clientName" class="block text-sm font-medium"
          >Ім'я клієнта</label
        >
        <input
          type="text"
          id="clientName"
          name="clientName"
          class="mt-1 block w-full border border-gray-600 rounded-md shadow-sm p-2 bg-gray-700 text-white focus:ring focus:ring-blue-500"
          placeholder="Введіть ім'я"
          v-model="contact.name"
        />
      </div>
      <div class="mb-4">
        <label for="contactInfo" class="block text-sm font-medium"
          >Контактна інформація(email)</label
        >
        <input
          type="email"
          id="contactInfo"
          name="contactInfo"
          class="mt-1 block w-full border border-gray-600 rounded-md shadow-sm p-2 bg-gray-700 text-white focus:ring focus:ring-blue-500"
          placeholder="Введіть контактну інформацію"
          v-model="contact.email"
        />
      </div>

      <div class="mb-4">
        <label for="product" class="block text-sm font-medium"
          >Назва товару</label
        >
        <select
          id="product"
          name="product"
          class="mt-1 block w-full border border-gray-600 rounded-md shadow-sm p-2 bg-gray-700 text-white focus:ring focus:ring-blue-500"
          v-model="product.id"
        >
          <option value="">Оберіть товар</option>
          <option
            v-for="product in products"
            :key="product.item_id"
            :value="product.item_id"
          >
            {{ product.name }} - {{ product.rate }} грн,
            {{ product.actual_available_stock || "0" }} шт.
          </option>
        </select>
      </div>
      <div class="mb-4"></div>

      <div class="mb-4">
        <label for="quantity" class="block text-sm font-medium"
          >Кількість</label
        >
        <input
          type="number"
          id="quantity"
          name="quantity"
          class="mt-1 block w-full border border-gray-600 rounded-md shadow-sm p-2 bg-gray-700 text-white focus:ring focus:ring-blue-500"
          placeholder="Введіть кількість"
          v-model="product.quantity"
        />
      </div>

      <div class="mb-4">
        <input
          type="checkbox"
          id="purchaseOrder"
          name="purchaseOrder"
          class="mr-2 leading-tight"
          :disabled="isPurchaseOrder"
          v-model="purchaseOrder"
        />
        <label for="purchaseOrder" class="text-sm"
          >Створити замовлення на закупівлю</label
        >
      </div>

      <div id="purchaseOrderDetails" class="mb-4 hidden">
        <h3 class="text-lg font-semibold">
          Товари для замовлення на закупівлю:
        </h3>
        <ul id="orderList" class="list-disc pl-5 mt-2"></ul>
      </div>

      <button
        type="submit"
        class="w-full bg-blue-600 text-white font-bold py-2 rounded hover:bg-blue-700"
      >
        Надіслати
      </button>
    </form>
  </div> -->

  <div class="bg-white shadow-md rounded-md px-6 py-8">
    <h2 class="text-2xl font-bold mb-6">New Sales Order</h2>

    <div class="mb-4">
      <label
        for="contactName"
        class="block text-red-600 text-sm font-bold mb-2"
      >
        Customer Name*
      </label>
      <div class="relative">
        <select
          id="contactName"
          class="w-full p-3 border rounded-md shadow-sm focus:outline-none focus:ring focus:ring-blue-300"
          v-model="selectContact"
        >
          <option value="">Select or add a customer</option>
          <option
            v-for="contact in contacts"
            :key="contact.contact_id"
            :value="contact"
          >
            {{ contact.contact_name }}
          </option>
        </select>
        <button
          class="absolute right-6 top-1/2 transform -translate-y-1/2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded cursor-pointer"
        >
          <i class=""> + </i>
        </button>
      </div>
    </div>

    <div class="mb-4">
      <label for="date" class="block text-gray-700 text-sm font-bold mb-2">
        Date
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
        Products*
      </label>
      <div class="relative">
        <select
          id="product"
          class="w-full p-3 border rounded-md shadow-sm focus:outline-none focus:ring focus:ring-blue-300"
          v-model="selectProduct"
          @change="addProduct"
        >
          <option value="">Select or add a product</option>
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
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from "vue";
import { getContacts, getItems } from "./api";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";

const selectedDate = ref(null);
const showDatePicker = ref(false);
const formatDate = (date) => {
  if (!date) return "";
  const options = { day: "2-digit", month: "short", year: "numeric" };
  return new Intl.DateTimeFormat("en-GB", options).format(date);
};

const selectContact = ref({});
const selectProduct = ref({});

const isPurchaseOrder = ref(false);
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

const addProduct = () => {
  addedProducts.value.push(selectProduct.value);
  console.log(addedProducts.value);
};

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
