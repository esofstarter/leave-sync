<script setup lang="ts">
  import { IconTrash, IconEdit } from "@starter-core/icons";
  import { useAuth } from "@websanova/vue-auth/src/v3.js";
  import { ref, computed } from "vue";
  import type { GetCountryResponse } from "../types";
  import ConfirmDialog from "@/components/ConfirmDialog/ConfirmDialog.vue";

  import {
    DashButton,
    DashLink,
    TableColumn,
    TableRow,
  } from "@starter-core/dash-ui/src";

  interface CountriesTableRowProps {
    country: GetCountryResponse;
    isEvenRow: boolean;
    deleteCountry: (id: number) => Promise<void>;
  }

  const { country, isEvenRow, deleteCountry } =
    defineProps<CountriesTableRowProps>();
  const auth = useAuth();

  const showConfirmDialog = ref(false);

  const confirmDelete = () => {
    deleteCountry(country.id);
    showConfirmDialog.value = false;
  };
</script>

<template>
  <TableRow :section="'body'" :is-even="isEvenRow">
    <!--kt-datatable__row&#45;&#45;even-->

    <TableColumn>
      {{ country.country_code }}
    </TableColumn>

    <TableColumn>
      {{ country.name }}
    </TableColumn>

    <TableColumn>
      <dash-link
        v-if="auth.user().permissions_array.includes('write_users')"
        :to="{ name: 'edit.country', params: { countryId: country.id } }"
        theme="primary"
        theme-mod="outline-hover"
        :icon="IconEdit"
      >
        {{ $t("buttons.edit") }}
      </dash-link>
    </TableColumn>

    <TableColumn>
      <DashButton
        v-if="auth.user().permissions_array.includes('delete_users')"
        :icon="IconTrash"
        theme="danger"
        size="sm"
        @click="showConfirmDialog = true"
        is-pill
        is-icon
      />
      <span v-else> - </span>
    </TableColumn>
  </TableRow>
  <ConfirmDialog
    :show="showConfirmDialog"
    message="Are you sure you want to remove this country?"
    @confirm="confirmDelete"
    @close="showConfirmDialog = false"
  />
</template>
<style cloped>
  .noClick {
    pointer-events: none;
  }
</style>
