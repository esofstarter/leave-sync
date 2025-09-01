<script setup lang="ts">
  import { onMounted } from "vue";
  import { LeaveRequestsDatatable } from "../components";
  import { PageWrapper } from "@/components";
  import { useRootStore } from "@/store/root";

  const { setBackUrl, setActiveClasses } = useRootStore();

  onMounted(() => {
    setBackUrl("/");
    setActiveClasses({
      main: "leave_requests",
      sub: "leave_requests",
      title: "leave_requests.title",
      hasFilters: "leave_requests",
    });
  });
</script>
<template>
  <PageWrapper>
    <LeaveRequestsDatatable />
  </PageWrapper>
</template>
