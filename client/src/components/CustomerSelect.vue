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
import ModalItem from "./ModalItem.vue";
import { onMounted, ref } from "vue";
import { getContacts, addContact } from "../api";

const selected = ref({});
const contacts = ref([]);

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
    selected.value = data.data.contact;
    isModalOpen.value = false;
  });
};

onMounted(() => {
  getContacts().then((data) => {
    contacts.value = data.data.contacts.filter(
      (contact) => contact.contact_type_formatted === "Customer"
    );
    console.log("Contacts", contacts.value);
  });
});
</script>

<style setup>
.modal_bg {
  background-color: rgba(0, 0, 0, 0.5);
}
</style>
