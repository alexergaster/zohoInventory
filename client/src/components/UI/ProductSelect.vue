<template>
  <div>
    <div class="mb-4">
      <div
        v-if="selected && selected !== 'modal'"
        class="p-3 border rounded-md shadow-sm bg-gray-100"
      >
        {{ selected.name }} - {{ selected.available_stock }} шт.
      </div>
      <select
        v-else
        class="w-full p-3 border rounded-md shadow-sm"
        v-model="selected"
        @change="updateSelectedProduct"
      >
        <option
          v-for="product in products"
          :key="product.item_id"
          :value="product"
        >
          {{ product.name }} - {{ product.available_stock }} шт.
        </option>
        <option value="modal" class="text-blue-500 bg-gray-200">
          Додати товар
        </option>
      </select>
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
import BaseInput from "./BaseInput.vue";
import ModalItem from "../modals/ModalItem.vue";
import { defineProps, defineEmits, ref, computed } from "vue";
import { addItem } from "../../api";

const props = defineProps(["selectedProduct", "products"]);
const emit = defineEmits(["update:product"]);

const productsAll = ref([]);

console.log(props.products);

const productData = ref({
  name: "",
  rate: "",
  purchase_rate: "",
  initial_stock: "",
});
const selected = ref(props.selectedProduct || null);
const errorMessage = ref("");
const isModalOpen = ref(false);

const handleConfirm = () => {
  errorMessage.value = "";
  const product = { ...productData.value };

  addItem(product).then((data) => {
    if (data.data.code === 0) {
      selected.value = data.data.item;
      isModalOpen.value = false;
      productData.value = {
        name: "",
        rate: "",
        purchase_rate: "",
        initial_stock: "",
      };
      emit("update:product", selected.value);
    } else {
      errorMessage.value = data.data.message;
    }
  });
};

const required = (value) => (!value ? "Це поле обов'язкове" : "");
const positiveNumber = (value) =>
  isNaN(value) || Number(value) <= 0 ? "Має бути позитивним числом" : "";

const updateSelectedProduct = () => {
  if (selected.value === "modal") {
    isModalOpen.value = true;
  } else {
    emit("update:product", selected.value);
  }
};
</script>
