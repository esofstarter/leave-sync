<script lang="ts" setup>
  import { computed } from "vue";
  import { useI18n } from "vue-i18n";
  import { useUserRoles } from "../composables";
  import { FormDropdown } from "@starter-core/dash-ui/src";

  const { t } = useI18n();

  const { isLoading: isFetchingRoles, data: roles } = useUserRoles();
  const role = defineModel("role", { required: true });

  const filteredRoles = computed(() => {
    return (roles.value || []).filter(r => r.id !== 4);
  });
</script>
<template>
  <form-dropdown
    v-if="!isFetchingRoles"
    v-model="role"
    id="role"
    :options="filteredRoles"
    :label="t('users.roles.label')"
    is-inline
  />
</template>
