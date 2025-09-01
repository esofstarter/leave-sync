<script lang="ts" setup>
import { useI18n } from "vue-i18n";
import { ref, onMounted } from "vue";
import axios from "axios";
import { FormDropdown } from "@starter-core/dash-ui/src";

const { t } = useI18n();
const country = defineModel("country", { required: true });
const countryOptions = ref<{ id: number; label: string; value: string; name: string }[]>([]);

const fetchCountries = async () => {
  try {
    const response = await axios.get("/country/all");
    countryOptions.value = response.data.map((country: any) => ({
      id: country.id,
      label: country.name,
      value: country.name,
      name: country.name,
    }));
  } catch (error) {
    console.error("Error fetching countries:", error);
  }
};

onMounted(fetchCountries);
</script>

<template>
  <form-dropdown
    v-model="country"
    id="role"
    :options="countryOptions"
    :label="'Country'"
    is-inline
  />
</template>
