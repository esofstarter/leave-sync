<script setup lang="ts">
  import { computed } from "vue";
  import { useLeaveTypesTable } from "../composables";
  import { useLeaveTypesForm } from "../composables/useLeaveTypesForm";
  import { LEAVE_TYPES_DATATABLE_COLUMNS } from "../constants";
  import LeaveTpyesTableRow from "./LeaveTpyesTableRow.vue";
  import LeaveTypesTableHeader from "./LeaveTypesTableHeader.vue";
  import {
    useDatatable,
    DatatableComponent,
    DatatableFilters,
    DatatableHeader,
    PaginationComponent,
  } from "@starter-core/dash-ui/src";

  const { query, onPaginationChange } = useDatatable();

  const { data, isLoading, isFetching, error, refetch } =
    useLeaveTypesTable(query);
  const { deleteLeaveType } = useLeaveTypesForm();

  const pagination = computed(() => data.value?.pagination ?? null);
  const leaveTypes = computed(() => data.value?.data ?? null);
</script>
<template>
  <DatatableComponent
    :query="query"
    :isLoading="isLoading || isFetching"
    :columns="LEAVE_TYPES_DATATABLE_COLUMNS"
    :error="error?.message"
  >
    <template #header>
      <DatatableHeader title="Leave Types">
        <LeaveTypesTableHeader />
      </DatatableHeader>
      <DatatableFilters />
    </template>
    <template v-if="leaveTypes" #default>
      <LeaveTpyesTableRow
        v-for="(leaveType, index) in leaveTypes"
        :key="leaveType.id"
        :columns="LEAVE_TYPES_DATATABLE_COLUMNS"
        :leaveType="leaveType"
        :is-even-row="index % 2 === 0"
        :deleteLeaveType="
          (id) => deleteLeaveType(id, { onSuccess: () => refetch() })
        "
      />
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
