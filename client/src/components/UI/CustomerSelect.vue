<template>
  <div>
    <div class="mb-4">
      <label class="block text-red-600 text-sm font-bold mb-2"
        >Обрати клієнта*</label
      >
      <div class="relative">
        <select
          class="w-full p-3 border rounded-md shadow-sm"
          v-model="selected"
          @change="selectContact"
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
          +
        </button>
      </div>
    </div>
    <ModalItem
      :isModalOpen="isModalOpen"
      @close="isModalOpen = false"
      @confirm="handleConfirm"
    >
      <template #title>Додати клієнта</template>
      <template #content>
        <BaseInput
          v-model="contactData.name"
          label="Ім'я клієнта*"
          id="name"
          :rules="[required]"
        />
        <BaseInput
          v-model="contactData.email"
          label="Email клієнта"
          id="email"
          :rules="[emailValidation]"
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

import { onMounted, defineEmits, ref } from "vue";
import { getContacts, addContact } from "../../api";

const required = (value) => (!value ? "Це поле обов'язкове" : "");
const emailValidation = (value) => {
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return !emailPattern.test(value) ? "Введіть коректний email" : "";
};
const emit = defineEmits(["update:contact"]);

const selected = ref({});
const contacts = ref([]);
const errorMessage = ref("");

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
    if (data.data.code === 0) {
      contacts.value.push(data.data.contact);
      selected.value = data.data.contact;
      isModalOpen.value = false;
      contactData.value = { name: "", email: "" };
      selectContact();
    } else {
      errorMessage.value = data.data.message;
    }
  });
};

const selectContact = () => {
  emit("update:contact", selected.value);
};

onMounted(() => {
  getContacts().then((data) => {
    contacts.value = data.data.contacts.filter(
      (contact) => contact.contact_type_formatted === "Customer"
    );
  });
});
</script>

<style setup>
.modal_bg {
  background-color: rgba(0, 0, 0, 0.5);
}
</style>
