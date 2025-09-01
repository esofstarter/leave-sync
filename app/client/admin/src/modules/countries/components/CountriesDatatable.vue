<script setup lang="ts">
  import { computed } from "vue";
  import { useCountriesTable } from "../composables";
  import { useCountriesForm } from "../composables/useCountriesForm";
  import { COUNTRIES_DATATABLE_COLUMNS } from "../constants";
  import CountriesTableHeader from "./CountriesTableHeader.vue";
  import CountriesTableRow from "./CountriesTableRow.vue";
  import {
    useDatatable,
    DatatableComponent,
    DatatableFilters,
    DatatableHeader,
    PaginationComponent,
  } from "@starter-core/dash-ui/src";

  const { query, onPaginationChange } = useDatatable();

  const { data, isLoading, isFetching, error, refetch } =
    useCountriesTable(query);
  const { deleteCountry } = useCountriesForm();

  const pagination = computed(() => data.value?.pagination ?? null);
  const countries = computed(() => data.value?.data ?? null);
</script>
<template>
  <DatatableComponent
    :query="query"
    :isLoading="isLoading || isFetching"
    :columns="COUNTRIES_DATATABLE_COLUMNS"
    :error="error?.message"
  >
    <template #header>
      <DatatableHeader title="Countries">
        <CountriesTableHeader />
      </DatatableHeader>
      <DatatableFilters />
    </template>
    <template v-if="countries" #default>
      <CountriesTableRow
        v-for="(country, index) in countries"
        :key="country.id"
        :columns="COUNTRIES_DATATABLE_COLUMNS"
        :country="country"
        :is-even-row="index % 2 === 0"
        :deleteCountry="
          (id) => deleteCountry(id, { onSuccess: () => refetch() })
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
