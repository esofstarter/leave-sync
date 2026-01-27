<script setup lang="ts">
  import { IconTrash, IconEdit } from "@starter-core/icons";
  import { useAuth } from "@websanova/vue-auth/src/v3.js";
  import { ref, computed } from "vue";
  import type { GetLeaveTypeResponse } from "../types";
  import ConfirmDialog from "@/components/ConfirmDialog/ConfirmDialog.vue";

  import {
    DashButton,
    DashLink,
    TableColumn,
    TableRow,
  } from "@starter-core/dash-ui/src";

  interface LeaveTypesTableRowProps {
    leaveType: GetLeaveTypeResponse;
    isEvenRow: boolean;
    deleteLeaveType: (id: number) => Promise<void>;
  }

  const { leaveType, isEvenRow, deleteLeaveType } =
    defineProps<LeaveTypesTableRowProps>();
  const auth = useAuth();

  const showConfirmDialog = ref(false);

  const confirmDelete = () => {
    deleteLeaveType(leaveType.id);
    showConfirmDialog.value = false;
  };
</script>

<template>
  <TableRow :section="'body'" :is-even="isEvenRow">
    <!--kt-datatable__row&#45;&#45;even-->

    <TableColumn>
      {{ leaveType.slug }}
    </TableColumn>

    <TableColumn>
      {{ leaveType.name }}
    </TableColumn>

    <TableColumn>
      <input
        style="border: none"
        readonly
        type="color"
        id="colorPicker"
        :value="leaveType.color"
        class="color-input noClick"
      />
    </TableColumn>

    <TableColumn>
      <dash-link
        v-if="auth.user().permissions_array.includes('write_users')"
        :to="{ name: 'edit.leave_type', params: { leaveTypeId: leaveType.id } }"
        theme="primary"
        theme-mod="outline-hover"
        :icon="IconEdit"
      >
        {{ $t("buttons.edit") }}
      </dash-link>
    </TableColumn>

    <TableColumn>
      <DashButton
        v-if="
          auth.user().permissions_array.includes('delete_users') &&
          leaveType.id >= 7
        "
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
    message="Are you sure you want to delete this leave type?"
    @confirm="confirmDelete"
    @close="showConfirmDialog = false"
  />
</template>
<style cloped>
  .noClick {
    pointer-events: none;
  }
</style>
