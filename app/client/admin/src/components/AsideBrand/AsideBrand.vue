<script setup lang="ts">
  import {
    IconAngledoubleleft,
    IconAngledoubleright,
  } from "@starter-core/icons";
  import { storeToRefs } from "pinia";
  import { computed } from "vue";
  import { useRootStore } from "@/store/root";
  import "./AsideBrand.scss";

  const rootStore = useRootStore();
  const { sidebarState, isSidebarMinimized } = storeToRefs(rootStore);

  const isLogoVisible = computed(
    () =>
      !isSidebarMinimized.value ||
      (isSidebarMinimized.value && sidebarState.value.minimizeHover),
  );
</script>

<template>
  <div class="kt-aside__brand">
    <div class="kt-aside__brand-logo">
      <router-link to="/admin/dashboard"> LeaveSync </router-link>
    </div>
  </div>
</template>
