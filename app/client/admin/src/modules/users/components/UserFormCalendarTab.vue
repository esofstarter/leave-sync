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
  // Internationalization
  const { t } = useI18n();

  // Props
  const props = defineProps<{ userId: number; country: number }>();

  // Reactive References
  const leaveDays = ref([]);
  const leaveTypes = ref([]);
  const nationalHolidays = ref([]);

  // Fetch Approved Leave Days
  const fetchApprovedLeaveDays = async () => {
    try {
      const response = await axios.get(`/leave_request/${props.userId}/approved_user`);
      leaveDays.value = response.data;
    } catch (error) {
      console.error("Error fetching leave requests:", error);
    }
  };

  // Fetch Leave Types
  const fetchLeaveTypes = async () => {
    try {
      const response = await axios.get("/leave_type/all");
      leaveTypes.value = response.data;
    } catch (error) {
      console.error("Error fetching leave types:", error);
    }
  };

  const fetchUserNationalHolidays = async () => {
    try {
      const response = await axios.get("/national_holiday/draw", {
        params: { isCountry: props.country },
      });
      nationalHolidays.value = response.data.data;
    } catch (error) {
      console.error("Error fetching national holidays:", error);
    }
  };

  // Get Leave Type Name by ID
  const getNameLeaveType = (id: number) => {
    const leaveType = leaveTypes.value.find((type) => type.id === id);
    return leaveType ? leaveType.name : "Unknown Leave Type";
  };

  const getNameLeaveColor = (id: number) => {
    const leaveType = leaveTypes.value.find((type) => type.id === id);
    return leaveType ? leaveType.color : "Unknown Leave Type";
  };

  // Map Leave Days to Calendar Events
  const calendarEvents = computed(() => {
    // Leave Events (Multi-Day Events)
    const leaveEvents = leaveDays.value.map((leave: any) => ({
      title: getNameLeaveType(leave.leave_type_id),
      start: leave.start_date,
      end: leave.end_date
        ? new Date(new Date(leave.end_date).getTime() + 24 * 60 * 60 * 1000)
            .toISOString()
            .split("T")[0] // Add 1 day to include the last day
        : leave.start_date,
      backgroundColor: getNameLeaveColor(leave.leave_type_id),
      textColor: "#fff",
    }));

    // National Holiday Events (Single-Day Events)
    const holidayEvents = nationalHolidays.value.map((holiday: any) => ({
      title: `Holiday`,
      start: holiday.date,
      backgroundColor: getNameLeaveColor(5),
      textColor: "#fff",
    }));

    return [...leaveEvents, ...holidayEvents]; // Combine both events
  });

  // Calendar Options
  const calendarOptions = computed(() => ({
    plugins: [dayGridPlugin],
    initialView: "dayGridMonth",
    weekends: true,
    firstDay: 1,
    events: calendarEvents.value,
  }));

  // Lifecycle Hook
  onMounted(() => {
    fetchApprovedLeaveDays();
    fetchLeaveTypes();
    fetchUserNationalHolidays();
  });
</script>

<template>
  <div class="kt-section">
    <div class="kt-section__body">
      <div>
        <div class="leaveTypes">
          <div
            class="leaveTypes_wrapper"
            v-for="(type, index) in leaveTypes"
            :key="index"
          >
            <span>{{ type.name }}</span>
            <span class="leaveTypes_color"
              ><div
                :style="`background-color: ${type.color}; width: 20px; height: 20px;`"
              ></div
            ></span>
            <span class="leaveTypes_separator"></span>
          </div>
        </div>
        <div
          class="kt-separator kt-separator--border-dashed kt-separator--space-lg"
        ></div>
        <div style="width: 65%; height: 70%">
          <FullCalendar :options="calendarOptions" />
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
  .kt-section {
    padding: 20px;
  }

  .kt-section__title {
    margin-bottom: 20px;
    color: #343a40;
  }

  .fc-event {
    border-radius: 4px;
    padding: 2px 4px;
    font-weight: bold;
  }

  .leaveTypes {
    display: flex;
    align-items: center;
    justify-content: flex-start;
  }

  .leaveTypes_wrapper {
    display: flex;
    align-items: center;
    width: max-content;
  }

  .leaveTypes_color {
    margin-left: 5px;
  }

  .leaveTypes_separator {
    margin-left: 20px;
    margin-right: 30px;
  }
</style>
