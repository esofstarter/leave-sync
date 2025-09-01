<script setup lang="ts">
  import { onMounted } from "vue";
  import { CountriesDatatable } from "../components";
  import { PageWrapper } from "@/components";
  import { useRootStore } from "@/store/root";

  const { setBackUrl, setActiveClasses } = useRootStore();

  onMounted(() => {
    setBackUrl("/");
    setActiveClasses({
      main: "countries",
      sub: "countries",
      title: "countries.title",
      hasFilters: "countries",
    });
  });
</script>
<template>
  <PageWrapper>
    <CountriesDatatable />
  </PageWrapper>
</template>
