<script lang="ts" setup>
  import { computed, ref, onMounted, watch } from "vue";
  import axios from "axios";
  import { useI18n } from "vue-i18n";
  import FlatPickr from "vue-flatpickr-component";
  import "flatpickr/dist/flatpickr.css";
  import { FormDropdownCountries } from "@starter-core/dash-ui/src";

  const { t } = useI18n();
  const date = defineModel("date", { required: true, type: String });
  const country = defineModel("country", { required: true, type: String });
  const year = defineModel("year", { required: true, type: Number });
  const countryOptions = ref<{ label: string; name: string }[]>([]);

  const baseConfig = computed(() => ({
    dateFormat: "Y-m-d", // value bound to v-model
    altInput: true,
    altFormat: "d/m/Y", // what the user sees
    locale: { firstDayOfWeek: 1 }, // Monday
    allowInput: true,
  }));

  // Watch date and set year
  watch(date, (newVal) => {
    if (newVal) {
      const parsed = new Date(newVal);
      if (!isNaN(parsed.getTime())) {
        year.value = parsed.getFullYear();
      }
    }
  });

  const fetchCountries = async () => {
    try {
      const response = await axios.get("/country/all");
      countryOptions.value = response.data.map((country: any) => ({
        label: country.name,
        name: country.name,
      }));
    } catch (error) {
      console.error("Error fetching countries:", error);
    }
  };

  onMounted(fetchCountries);
</script>

<template>
  <div class="kt-section">
    <div class="kt-section__body">
      <form-dropdown-countries
        v-model="country"
        id="role"
        :readonly="false"
        :options="countryOptions"
        :label="'Country'"
        is-inline
      />
      <!-- Removed manual year input -->
      <div>
        <label class="form-group__label" for="date">Date</label>
        <FlatPickr
          id="date"
          name="date"
          v-model="date"
          :config="baseConfig"
          placeholder="dd/mm/yyyy"
        />
      </div>
      <div>
        <label class="form-group__label">Year</label>
        <div>{{ year }}</div>
      </div>
    </div>
  </div>
</template>
