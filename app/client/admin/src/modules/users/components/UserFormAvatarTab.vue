<script lang="ts" setup>
  import dayGridPlugin from "@fullcalendar/daygrid";
  import FullCalendar from "@fullcalendar/vue3";
  import axios from "axios";
  import { ref, onMounted, computed } from "vue";
  import { useI18n } from "vue-i18n";
  import {
    PortletComponent,
    PortletBody,
    PortletHead,
    PortletHeadLabel,
  } from "@starter-core/dash-ui/src";
  const { t } = useI18n();

  // Props
  const props = defineProps<{ userId: number; country: number }>();

</script>

<template>
  <div class="kt-section">
    <div class="kt-section__body">
      <div>
      </div>
    </div>
  </div>
</template>
