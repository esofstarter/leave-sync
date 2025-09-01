<script setup lang="ts">
  import { BadgeComponent } from "@starter-core/dash-ui/src";

  const { isPaid } = defineProps<{ isPaid: boolean }>();
</script>
<template>
  <span>
    <BadgeComponent :theme="isPaid ? 'danger' : 'success'" is-inline is-pill>
      {{ isPaid ? $t("users.status.disabled") : $t("users.status.enabled") }}
    </BadgeComponent>
  </span>
</template>
