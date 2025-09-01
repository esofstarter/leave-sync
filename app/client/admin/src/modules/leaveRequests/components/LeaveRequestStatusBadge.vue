<script setup lang="ts">
  import { computed, ref } from "vue";
  import { BadgeComponent } from "@starter-core/dash-ui/src";

  const props = defineProps<{
    status?: number;
  }>();

  const statusName = computed(() => {
    switch (props.status) {
      case 0:
        return "Pending";
      case 1:
        return "Declined";
      case 2:
        return "Approved";
      default:
        return "Unknown";
    }
  });

  const badgeTheme = computed(() => {
    switch (props.status) {
      case 0:
        return "primary";
      case 1:
        return "danger";
      case 2:
        return "success";
      default:
        return "";
    }
  });
</script>
<template>
  <span>
    <BadgeComponent :theme="badgeTheme" is-inline is-pill>
      {{ statusName }}
    </BadgeComponent>
  </span>
</template>
