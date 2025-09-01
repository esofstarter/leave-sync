<script lang="ts" setup>
  import { useI18n } from "vue-i18n";
  import { useUserRoles } from "../composables";
  import {
    FormDropdownManagers,
    FormDropdownLeaveTypes,
  } from "@starter-core/dash-ui/src";

  const { t } = useI18n();

  const model = defineModel("model", { required: true });
  const optionsData = defineModel("optionsData", { required: true });
  const readonly = defineModel("readonly", { required: true, default: false });
</script>
<template>
  <form-dropdown-managers
    v-model="model"
    id="manager"
    :options="optionsData"
    :readonly="readonly"
    label="Assign To"
    is-inline
  />
</template>
