<script setup lang="ts">
  import {
    IconAirpods,
    IconChartpie,
    IconDollar,
    IconLibrary,
  } from "@starter-core/icons";
  import { useAuth } from "@websanova/vue-auth/src/v3.js";
  import axios from "axios";
  import { ref, onMounted } from "vue";
  import { PageWrapper } from "@/components";
  // import { get } from "@/services/HTTP";
  import { leaveRequest } from "@/modules/leaveRequests/constants";
  import { useRootStore } from "@/store/root";
  import {
    PortletComponent,
    PortletBody,
    PortletHead,
    PortletHeadLabel,
    ContentLoader,
    AccordionContent,
    AccordionItem,
    DashLink,
  } from "@starter-core/dash-ui/src";
  import LeaveCalendarPage from "@/modules/leaveRequests/pages/LeaveCalendarPage.vue";
  // const categories = ref([]);
  const leaveTypes = ref([]);
  const users = ref([]);
  const leaveRequests = ref([]);
  const isLoading = ref(false);
  const auth = useAuth();

  const { setActiveClasses } = useRootStore();
  onMounted(() => {
    fetchLeaveTypes();
    fetchUsers();
    fetchRequests();
    isLoading.value = true;
    setActiveClasses({
      main: "item_dashboard",
      sub: "item_dashboard",
      title: "strings.dashboard",
    });
  });

  const fetchUsers = async () => {
    try {
      const response = await axios.get("/user/all");
      users.value = response.data;
    } catch (error) {
      console.error("Error fetching leave types:", error);
    }
  };

  const fetchRequests = async () => {
    try {
      const response = await axios.get("/leave_request/pending");
      leaveRequests.value = response.data;
    } catch (error) {
      console.error("Error fetching leave types:", error);
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

  const formatDate = (dateString: string) => {
    if (!dateString) return "Invalid Date";
    const date = new Date(dateString);
    return date.toLocaleDateString("en-GB", {
      year: "numeric",
      month: "short",
      day: "2-digit",
    });
  };
</script>
<template>
  <PageWrapper class="display-dashboard">
    <div class="row col-4 main_dashboard-data">
      <div
        v-if="auth.user().permissions_array.includes('write_users')"
      >
        <PortletComponent isBordered>
          <PortletHead>
            <PortletHeadLabel> Pending Leave Requests </PortletHeadLabel>
          </PortletHead>
          <PortletBody>
            <table>
              <tr>
                <th>No.</th>
                <th>From</th>
                <th>From (Date)</th>
                <th>To (Date)</th>
                <th>Link</th>
              </tr>
              <tr v-for="(leave, index) in leaveRequests" :key="index">
                <td>{{ index + 1 }}.</td>
                <td>
                  {{ leave.user.first_name + " " + leave.user.last_name }}
                </td>
                <td>
                  {{ formatDate(leave.start_date) }}
                </td>
                <td>
                  {{
                    leave.end_date ? formatDate(leave.end_date) : "Single Day"
                  }}
                </td>
                <td>
                  <router-link
                    :to="`/admin/leave_request/${leave.id}/confirmation`"
                    >View</router-link
                  >
                </td>
              </tr>
            </table>
          </PortletBody>
        </PortletComponent>
      </div>
      <div>
        <PortletComponent isBordered>
          <PortletHead>
            <PortletHeadLabel> Leave Types </PortletHeadLabel>
          </PortletHead>
          <PortletBody>
            <table>
              <tr>
                <th>No.</th>
                <th>Name</th>
                <th>Color</th>
              </tr>
              <tr v-for="(type, index) in leaveTypes" :key="index">
                <td>{{ type.id }}.</td>
                <td>{{ type.name }}</td>
                <td><span class="leaveTypes_color">
                  <div :style="`background-color: ${type.color}; width: 20px; height: 20px;`"></div></span>
                </td>
              </tr>
            </table>
          </PortletBody>
        </PortletComponent>
      </div>
      <div
        v-if="auth.user().role == 1 || auth.user().role == 4"
      >
        <PortletComponent isBordered>
          <PortletHead>
            <PortletHeadLabel> Paid Leaves Left </PortletHeadLabel>
          </PortletHead>
          <PortletBody>
           <div class="table-scroll">
            <table>
              <tr>
                <th>No.</th>
                <th>Name</th>
                <th>Days Left</th>
              </tr>

              <tr v-for="(user, index) in users" :key="user.id || index">
                <td>{{ index + 1 }}.</td>
                <td>{{ user.first_name }}</td>
                <td>{{ user.paid_leaves_left }}</td>
              </tr>
            </table>
           </div>
          </PortletBody>
        </PortletComponent>
      </div>
    </div>
    <LeaveCalendarPage :leaveRequestsPending="leaveRequests"/>
  </PageWrapper>
</template>
<style>
.display-dashboard > div {
  display: flex;
}

.main_dashboard-data {
  display: flex;
  gap: 20px;

  /* make it scroll */
  max-height: 100vh;   /* or calc(100vh - headerHeight) if you have a fixed header */
  overflow-y: auto;
  padding-right: 8px;  /* optional: space for scrollbar */
}

.table-scroll {
  max-height: 369px;
  overflow-y: auto;
  overflow-x: auto;
}

/* Optional: prevent ugly wrapping */
.table-scroll table {
  width: 100%;
}

</style>