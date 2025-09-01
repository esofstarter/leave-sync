<script setup lang="ts">
  import { onMounted } from "vue";
  import { NationalHolidaysDatatable } from "../components";
  import { PageWrapper } from "@/components";
  import { useRootStore } from "@/store/root";

  const { setBackUrl, setActiveClasses } = useRootStore();

  onMounted(() => {
    setBackUrl("/");
    setActiveClasses({
      main: "national_holidays",
      sub: "national_holidays",
      title: "national_holidays.title",
      hasFilters: "national_holidays",
    });
  });
</script>
<template>
  <PageWrapper>
    <NationalHolidaysDatatable />
  </PageWrapper>
</template>
