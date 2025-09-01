<script setup lang="ts">
  import { computed } from "vue";
  import { useUsersForm } from "../composables/useUsersForm";
  import { useUsersTable } from "../composables/useUsersTable";
  import { USERS_DATATABLE_COLUMNS } from "../constants";
  import UsersTableHeader from "./UsersTableHeader.vue";
  import UsersTableRow from "./UsersTableRow.vue";
  import {
    useDatatable,
    DatatableComponent,
    DatatableFilters,
    DatatableHeader,
    PaginationComponent,
  } from "@starter-core/dash-ui/src";

  const { query, onPaginationChange } = useDatatable();
  const { data, isLoading, isFetching, error, refetch } = useUsersTable(query);
  const { deleteUser } = useUsersForm();

  const users = computed(() => data.value?.data ?? []);
  const pagination = computed(() => data.value?.pagination ?? null);
  const userFilterOptions = [
    { id: 0, name: "Select Role", value: "" },
    { id: 1, name: "Admin", value: "admin" },
    { id: 2, name: "Manager", value: "manager" },
    { id: 3, name: "Developer", value: "developer" },
  ];
</script>

<template>
  <DatatableComponent
    :query="query"
    :isLoading="isLoading || isFetching"
    :columns="USERS_DATATABLE_COLUMNS"
    :error="error?.message"
  >
    <template #header>
      <DatatableHeader title="Users">
        <UsersTableHeader />
      </DatatableHeader>
      <DatatableFilters :options="userFilterOptions" />
    </template>

    <template v-if="users.length > 0" #default>
      <UsersTableRow
        v-for="(user, index) in users"
        :key="user.id"
        :columns="USERS_DATATABLE_COLUMNS"
        :user="user"
        :is-even-row="index % 2 === 0"
        :deleteUser="(id) => deleteUser(id, { onSuccess: () => refetch() })"
      />
    </template>
    <template v-else #default>
      <p class="text-center">No users found.</p>
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
