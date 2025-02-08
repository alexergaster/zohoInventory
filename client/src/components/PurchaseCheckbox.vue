<template>
  <div class="mt-4">
    <label for="purchases" class="flex items-center space-x-2 cursor-pointer">
      <input
        type="checkbox"
        id="purchases"
        class="peer hidden"
        :disabled="isPurchaseDisabled"
        v-model="purchaseOrder"
        @click="() => emit('update:purchaseOrder', !purchaseOrder)"
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
</template>
<script setup>
import { ref, computed, watch, defineProps, defineEmits } from "vue";

const props = defineProps({
  product: Array,
});

const emit = defineEmits(["update:purchaseOrder"]);

let isPurchaseDisabled = ref(true);
let purchaseOrder = ref(false);

const computePurchase = computed(() => {
  return !props.product.some(
    (product) => product.quantity >= product.available_stock
  );
});

watch(
  () => props.product,
  () => (isPurchaseDisabled.value = computePurchase.value),
  { deep: true }
);
</script>
