<script setup lang="ts">
  import axios from "axios";
  import { computed, ref, onMounted } from "vue";
  import { useLeaveRequestsForm } from "../composables/useLeaveRequestsForm";
  import { useLeaveRequestsTable } from "../composables/useLeaveRequestsTable";
  import { LEAVE_REQUESTS_DATATABLE_COLUMNS } from "../constants";
  import LeaveRequestsTableHeader from "./LeaveRequestsTableHeader.vue";
  import LeaveRequestsTableRow from "./LeaveRequestsTableRow.vue";
  import {
    useDatatable,
    DatatableComponent,
    DatatableFilters,
    DatatableHeader,
    PaginationComponent,
  } from "@starter-core/dash-ui/src";
  import { useRoute } from "vue-router";

  const { query, onPaginationChange } = useDatatable();
  const { data, isLoading, isFetching, error, refetch } =
    useLeaveRequestsTable(query);
  const { deleteLeaveRequest, downloadLeaveRequestPDF } =
    useLeaveRequestsForm();
  const route = useRoute();

  const pagination = computed(() => data.value?.pagination ?? null);
  const leaveRequests = computed(() => data.value?.data ?? []);
  const isAllPage = computed(() => route.name == "leave_requests_all");
  const documents = ref([]);

  const fetchDocuments = async () => {
    try {
      const response = await axios.get("/document/all");
      documents.value = response.data;
    } catch (error) {
      console.error("Error fetching leave types:", error);
    }
  };

  onMounted(() => {
    fetchDocuments();
  });
</script>

<template>
  <DatatableComponent
    :query="query"
    :isLoading="isLoading || isFetching"
    :columns="LEAVE_REQUESTS_DATATABLE_COLUMNS"
    :error="error?.message"
  >
    <template #header>
      <DatatableHeader title="Leave Requests">
        <LeaveRequestsTableHeader />
      </DatatableHeader>
      <DatatableFilters />
    </template>

    <template v-if="leaveRequests.length > 0" #default>
      <LeaveRequestsTableRow
        v-for="(leaveRequest, index) in leaveRequests"
        :key="leaveRequest.id"
        :leaveRequest="leaveRequest"
        :isEvenRow="index % 2 === 0"
        :deleteLeaveRequest="
          (id) => deleteLeaveRequest(id, { onSuccess: () => refetch() })
        "
        :downloadLeaveRequestPDF="
          (file_name) => downloadLeaveRequestPDF(file_name)
        "
        :documents="documents"
        :isAllPage="isAllPage"
      />
    </template>
    <template v-else #default>
      <p class="text-center">No leave requests found.</p>
    </template>

    <template v-if="pagination" #pagination>
      <PaginationComponent
        :pagination="pagination"
        :isLoading="isLoading"
        @change="onPaginationChange"
      />
    </template>
  </DatatableComponent>
</template>
