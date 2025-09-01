<script setup lang="ts">
  import { onMounted } from "vue";
  import { LeaveTypesDatatable } from "../components";
  import { PageWrapper } from "@/components";
  import { useRootStore } from "@/store/root";

  const { setBackUrl, setActiveClasses } = useRootStore();

  onMounted(() => {
    setBackUrl("/");
    setActiveClasses({
      main: "leave_types",
      sub: "leave_types",
      title: "leave_types.title",
      hasFilters: "leave_types",
    });
  });
</script>
<template>
  <PageWrapper>
    <LeaveTypesDatatable />
  </PageWrapper>
</template>
