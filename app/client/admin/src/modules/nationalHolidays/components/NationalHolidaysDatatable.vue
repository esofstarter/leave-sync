<script setup lang="ts">
  import { computed } from "vue";
  import { useLeaveTypesTable } from "../composables";
  import { useNationalHolidaysForm } from "../composables/useNationalHolidaysForm";
  import { NATIONAL_HOLIDAYS_DATATABLE_COLUMNS } from "../constants";
  import NationalHolidaysTableRow from "./NationalHolidaysTableRow.vue";
  import LeaveTypesTableHeader from "./NationalHolidaysTableHeader.vue";
  import {
    useDatatable,
    DatatableComponent,
    DatatableHolidaysFilters,
    DatatableHeader,
    PaginationComponent,
  } from "@starter-core/dash-ui/src";

  const { query, onPaginationChange } = useDatatable();

  const { data, isLoading, isFetching, error, refetch } =
    useLeaveTypesTable(query);
  const { deleteNationalHoliday } = useNationalHolidaysForm();

  const pagination = computed(() => data.value?.pagination ?? null);
  const nationalHolidays = computed(() => data.value?.data ?? null);
</script>
<template>
  <DatatableComponent
    :query="query"
    :isLoading="isLoading || isFetching"
    :columns="NATIONAL_HOLIDAYS_DATATABLE_COLUMNS"
    :error="error?.message"
  >
    <template #header>
      <DatatableHeader title="National Holidays">
        <LeaveTypesTableHeader />
      </DatatableHeader>
      <DatatableHolidaysFilters />
    </template>
    <template v-if="nationalHolidays" #default>
      <NationalHolidaysTableRow
        v-for="(holiday, index) in nationalHolidays"
        :key="holiday.id"
        :holiday="holiday"
        :is-even-row="index % 2 === 0"
        :deleteNationalHoliday="
          (id) => deleteNationalHoliday(id, { onSuccess: () => refetch() })
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
