<script lang="ts" setup>
  import { IconAdduser } from "@starter-core/icons";
  import { useAuth } from "@websanova/vue-auth/src/v3.js";
  import { computed } from "vue";
  import { DashLink } from "@starter-core/dash-ui/src";

  const auth = useAuth();

  const isUserAllowedToCreate = computed(() =>
    auth.user().permissions_array.includes("write_users"),
  );
</script>
<template>
  <DashLink
    v-if="isUserAllowedToCreate"
    :to="{ name: 'add.national_holiday' }"
    :icon="IconAdduser"
    theme="secondary"
  >
    {{ $t("admin.national_holidays.add") }}
  </DashLink>
</template>
