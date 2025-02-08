<template>
  <div>
    <div class="mb-4">
      <label class="block text-red-600 text-sm font-bold mb-2">Товари*</label>
      <div class="relative">
        <select
          class="w-full p-3 border rounded-md shadow-sm"
          v-model="selected"
          @change="addProduct"
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
          @click="isModalOpen = true"
          class="absolute right-6 top-1/2 transform -translate-y-1/2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded cursor-pointer"
        >
          <i> + </i>
        </button>
      </div>
    </div>
    <ModalItem
      :isModalOpen="isModalOpen"
      @close="isModalOpen = false"
      @confirm="handleConfirm"
    >
      <template #title>Додати товар</template>
      <template #content>
        <BaseInput
          v-model="productData.name"
          label="Назва товару*"
          id="name"
          :rules="[required]"
        />
        <BaseInput
          v-model="productData.rate"
          label="Ціна продажу товару*"
          id="rate"
          type="number"
          :rules="[required, positiveNumber]"
        />

        <BaseInput
          v-model="productData.purchase_rate"
          label="Ціна закупівлі товару*"
          id="purchase_rate"
          type="number"
          :rules="[required, positiveNumber]"
        />

        <BaseInput
          v-model="productData.initial_stock"
          label="Кількість товару*"
          id="initial_stock"
          type="number"
          :rules="[required, positiveNumber]"
        />

        <div
          v-if="errorMessage.length"
          class="mb-4 p-2 border-red-600 rounded border-2 text-center"
        >
          <p class="block text-red-600 text-sm">{{ errorMessage }}</p>
        </div>
      </template>
    </ModalItem>
  </div>
</template>

<script setup>
import ModalItem from "../modals/ModalItem.vue";
import BaseInput from "./BaseInput.vue";

import { defineEmits, onMounted, ref } from "vue";
import { getItems, addItem } from "../../api";

const emit = defineEmits(["update:products"]);

const required = (value) => (!value ? "Це поле обов'язкове" : "");
const positiveNumber = (value) =>
  isNaN(value) || Number(value) <= 0 ? "Має бути позитивним числом" : "";

const products = ref([]);
const selected = ref({});
const errorMessage = ref("");

const productData = ref({
  name: "",
  rate: "",
  purchase_rate: "",
  initial_stock: "",
});

const isModalOpen = ref(false);
const handleConfirm = () => {
  errorMessage.value = "";
  const product = { ...productData.value };

  addItem(product).then((data) => {
    if (data.data.code === 0) {
      selected.value = data.data.item;
      products.value.push(data.data.item);
      isModalOpen.value = false;
      productData.value = {
        name: "",
        rate: "",
        purchase_rate: "",
        initial_stock: "",
      };
      addProduct();
    } else {
      errorMessage.value = data.data.message;
    }
  });
};

const addProduct = () => {
  selected.value.quantity = 1;
  emit("update:products", selected.value);
};

onMounted(() => {
  getItems().then((data) => {
    products.value = data.data.items.filter(
      (product) => product.status !== "inactive"
    );
    console.log("Products", products.value);
  });
});
</script>
