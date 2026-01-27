<script setup lang="ts">
  import dayGridPlugin from "@fullcalendar/daygrid";
  import FullCalendar from "@fullcalendar/vue3";
  import axios from "axios";
  import { onMounted, ref, computed } from "vue";
  import { PageWrapper } from "@/components";
  import { useRootStore } from "@/store/root";

  // Store
  const { setBackUrl } = useRootStore();

  // Set back URL when the page is mounted
  onMounted(() => {
    setBackUrl("/");
  });

  const props = defineProps<{
    leaveRequestsPending?: Array<any>;
  }>();

  // Reactive References
  const leaveDays = ref([]);
  const nationalHolidays = ref([]);
  const leaveTypes = ref([]);

  // Fetch Leave Days for All Users (Only Approved)
  const fetchAllApprovedLeaveDays = async () => {
    try {
      const response = await axios.get("/leave_request/approved");
      leaveDays.value = response.data;
    } catch (error) {
      console.error("Error fetching leave requests:", error);
    }
  };

  const fetchAllNationalHolidays = async () => {
    try {
      const response = await axios.get("/national_holiday/all");
      nationalHolidays.value = response.data;
    } catch (error) {
      console.error("Error fetching national holidays:", error);
    }
  };

  const fetchLeaveTypes = async () => {
    try {
      const response = await axios.get("/leave_type/all");
      leaveTypes.value = response.data;
    } catch (error) {
      console.error("Error fetching leave types:", error);
    }
  };

  const getLeaveTypeColor = (id: number) => {
    const leaveType = leaveTypes.value.find((u: any) => u.id === id);
    return leaveType ? leaveType.color : "Unknown Leave Type";
  };

  const hexToRgba = (hex: string, alpha = 1) => {
    const m = hex?.trim().match(/^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i);
    if (!m) return hex || "#999999";
    const r = parseInt(m[1], 16);
    const g = parseInt(m[2], 16);
    const b = parseInt(m[3], 16);
    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
  };

  const addDays = (isoDate: string, days: number) => {
    const d = new Date(isoDate + "T00:00:00Z");
    d.setUTCDate(d.getUTCDate() + days);
    return d.toISOString().slice(0, 10);
  };

  const calendarEvents = computed(() => {
    const leaveEvents = leaveDays.value.map((leave: any) => ({
      
      title: leave?.user
        ? `${leave.user.first_name ?? ""} ${leave.user.last_name ?? ""}`.trim()
        : "Leave",
      start: leave.start_date,
      end: leave.end_date
        ? new Date(new Date(leave.end_date).getTime() + 24 * 60 * 60 * 1000)
            .toISOString()
            .split("T")[0] // Add 1 day
        : leave.start_date,
      backgroundColor: getLeaveTypeColor(leave.leave_type_id),
      textColor: "black",
    }));

    const holidayEvents = nationalHolidays.value.map((holiday: any) => ({
      title: holiday.country + "n Holiday",
      start: holiday.date,
      end: new Date(new Date(holiday.date).getTime() + 24 * 60 * 60 * 1000)
        .toISOString()
        .split("T")[0], // Add 1 day for holidays too
      backgroundColor: "#6326F2",
      textColor: "white",
    }));

    const pendingEvents = computed(() =>
    (props.leaveRequestsPending || []).map((leave: any) => {
      // use nested leave_type.color if present, else lookup by id
      const baseHex = leave.leave_type?.color || getLeaveTypeColor(leave.leave_type_id);
      const bg = hexToRgba(baseHex, 0.35); // semi-transparent
      const border = hexToRgba(baseHex, 0.9); // stronger border line
      return {
        title: `${leave.user.first_name} ${leave.user.last_name} (pending)`,
        start: leave.start_date,
        end: leave.end_date ? addDays(leave.end_date, 1) : leave.start_date,
        backgroundColor: bg,
        borderColor: border,
        textColor: "black",
        extendedProps: { kind: "pending", leaveId: leave.id, status: leave.status },
      };
    })
  );


    return [...leaveEvents, ...holidayEvents, ...pendingEvents.value];
  });

  // Calendar Options
  const calendarOptions = computed(() => ({
    plugins: [dayGridPlugin],
    initialView: "dayGridMonth",
    weekends: true,
    firstDay: 1,
    events: calendarEvents.value,
  }));

  // Fetch Data on Mount
  onMounted(() => {
    fetchAllApprovedLeaveDays();
    fetchAllNationalHolidays();
    fetchLeaveTypes();
  });
</script>

<template>
  <PageWrapper class="calendar_main_div">
    <div style="width: 100%;">
      <FullCalendar :options="calendarOptions" />
    </div>
  </PageWrapper>
</template>

<style scoped>
.bg {
  background-color: white;
}
  .page-title {
    margin-bottom: 20px;
    font-size: 24px;
    font-weight: bold;
    color: #343a40;
    text-align: center;
  }

  .fc-event {
    border-radius: 4px;
    padding: 2px 4px;
    font-weight: bold;
  }

  .leaveTypes {
    padding-left: 20px;
    margin-bottom: 23px;
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

  .calendar_main_div.page-wrapper__content {
    padding-top: 0 !important;
  }
</style>
