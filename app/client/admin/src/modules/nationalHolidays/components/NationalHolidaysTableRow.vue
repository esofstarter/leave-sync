<script setup lang="ts">
  import { IconTrash, IconEdit } from "@starter-core/icons";
  import { useAuth } from "@websanova/vue-auth/src/v3.js";
  import { ref, computed } from "vue";
  import type { GetNationalHolidayResponse } from "../types";
  import ConfirmDialog from "@/components/ConfirmDialog/ConfirmDialog.vue";

  import {
    DashButton,
    DashLink,
    TableColumn,
    TableRow,
  } from "@starter-core/dash-ui/src";

  interface LeaveTypesTableRowProps {
    holiday: GetNationalHolidayResponse;
    isEvenRow: boolean;
    deleteNationalHoliday: (id: number) => Promise<void>;
  }

  const { holiday, isEvenRow, deleteNationalHoliday } =
    defineProps<LeaveTypesTableRowProps>();
  const auth = useAuth();

  const showConfirmDialog = ref(false);

  const confirmDelete = () => {
    deleteNationalHoliday(holiday.id);
    showConfirmDialog.value = false;
  };

</script>

<template>
  <TableRow :section="'body'" :is-even="isEvenRow">
    <!--kt-datatable__row&#45;&#45;even-->

    <TableColumn>
      {{ holiday.date }}
    </TableColumn>

    <TableColumn>
      {{ holiday.country }}
    </TableColumn>

    <TableColumn>
      {{ holiday.year }}
    </TableColumn>

    <TableColumn>
      <dash-link
        v-if="auth.user().permissions_array.includes('write_users')"
        :to="{ name: 'edit.national_holiday', params: { nationalHolidayId: holiday.id } }"
        theme="primary"
        theme-mod="outline-hover"
        :icon="IconEdit"
      >
        {{ $t("buttons.edit") }}
      </dash-link>
    </TableColumn>

    <TableColumn>
      <DashButton
        :icon="IconTrash"
        theme="danger"
        size="sm"
        @click="showConfirmDialog = true"
        is-pill
        is-icon
      />
    </TableColumn>
  </TableRow>
  <ConfirmDialog
    :show="showConfirmDialog"
    message="Are you sure you want to delete this holiday?"
    @confirm="confirmDelete"
    @close="showConfirmDialog = false"
  />
</template>
<style cloped>
  .noClick {
    pointer-events: none;
  }
</style>
