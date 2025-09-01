<script setup lang="ts">
  import { IconTrash, IconEdit } from "@starter-core/icons";
  import { useAuth } from "@websanova/vue-auth/src/v3.js";
  import { ref } from "vue";
  import type { GetLeaveRequestResponse } from "../types";
  import LeaveRequestStatusBadge from "./LeaveRequestStatusBadge.vue";
  import ConfirmDialog from "@/components/ConfirmDialog/ConfirmDialog.vue";
  import {
    DashButton,
    DashLink,
    TableColumn,
    TableRow,
  } from "@starter-core/dash-ui/src";

  interface LeaveRequestsTableRowProps {
    leaveRequest: GetLeaveRequestResponse;
    isEvenRow: boolean;
    deleteLeaveRequest: (id: number) => Promise<void>;
    downloadLeaveRequestPDF: (file_name: string) => Promise<void>;
    documents: Array<any>;
    isAllPage: boolean;
  }

  const auth = useAuth();
  const { leaveRequest, isEvenRow, deleteLeaveRequest, downloadLeaveRequestPDF, documents, isAllPage } =
    defineProps<LeaveRequestsTableRowProps>();

  const showConfirmDialog = ref(false);

  const confirmDelete = () => {
    deleteLeaveRequest(leaveRequest.id);
    showConfirmDialog.value = false;
  };

  const getFileUrl = (fileName: string) => {
    return `${window.location.origin}/storage/${fileName}`;
  };

  const formatDateEU = (val: string | null | undefined) => {
    if (!val) return '';
    const s = String(val).slice(0, 10); // handles 'YYYY-MM-DD' or 'YYYY-MM-DDTHH:mm:ss...'
    const [y, m, d] = s.split('-');
    return (y && m && d) ? `${d}/${m}/${y}` : s; // fallback to raw if unexpected
  };
</script>

<template>
  <template v-if="true">
    <TableRow :section="'body'" :is-even="isEvenRow">
      <TableColumn>
        {{ leaveRequest.leaveType.name }}
      </TableColumn>

      <TableColumn>
        <span v-if="isAllPage">
          {{ leaveRequest.user.first_name }}
          {{ leaveRequest.user.last_name }}
        </span>
        <span v-else>
          -
        </span>
        
      </TableColumn>

      <TableColumn>
        {{ leaveRequest.requestToUser.first_name }}
        {{ leaveRequest.requestToUser.last_name }}
      </TableColumn>

      <TableColumn>
        <LeaveRequestStatusBadge :status="leaveRequest.is_confirmed" />
      </TableColumn>

      <TableColumn>
        {{ formatDateEU(leaveRequest.start_date) }}
      </TableColumn>

      <TableColumn>
        {{ leaveRequest.end_date ? formatDateEU(leaveRequest.end_date) : "Single Day" }}
      </TableColumn>

      <TableColumn>
        {{ leaveRequest.days }}
      </TableColumn>

      <TableColumn>
        <div v-if="documents.length">
          <div v-for="doc in documents" :key="doc.id">
            <a
              v-if="doc.leave_request_id === leaveRequest.id"
              :href="getFileUrl(doc.file_name)"
              target="_blank"
              rel="noopener noreferrer"
              class="pdf"
            >
              {{ doc.file_name }}
            </a>
          </div>
        </div>
        <div v-else>
          -
        </div>
      </TableColumn>

      <TableColumn>
        <DashLink
          v-if="
            auth.user().permissions_array.includes('delete_requests') &&
            (((leaveRequest.is_confirmed == 0 || leaveRequest.is_confirmed == 1) && auth.user().role !== 1) ||
            (auth.user().role == 1))
          "
          :to="{
            name: 'edit.leave_request',
            params: { leaveRequestId: leaveRequest.id },
          }"
          theme="primary"
          theme-mod="outline-hover"
          :icon="IconEdit"
        >
          {{ $t("buttons.edit") }}
        </DashLink>
        <span v-else>-</span>
      </TableColumn>

      <TableColumn>
        <DashButton
          v-if="
            auth.user().permissions_array.includes('delete_requests') &&
            (((leaveRequest.is_confirmed == 0 || leaveRequest.is_confirmed == 1) && auth.user().role !== 1) ||
            (auth.user().role == 1))
          "
          :icon="IconTrash"
          theme="danger"
          size="sm"
          @click="showConfirmDialog = true"
          is-pill
          is-icon
        />
      </TableColumn>
    </TableRow>

    <!-- Confirmation Dialog -->
    <ConfirmDialog
      :show="showConfirmDialog"
      message="Are you sure you want to delete this leave request?"
      @confirm="confirmDelete"
      @close="showConfirmDialog = false"
    />
  </template>
</template>
<style>
.pdf:hover {
 cursor: pointer;
 border-bottom: 0.3px solid black;
}
</style>