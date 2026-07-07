<script lang="ts" setup>
  import axios, { all } from "axios";
  import { ref, onMounted, computed } from "vue";
  import { useI18n } from "vue-i18n";
  import LeaveRequestsDropdown from "./LeaveRequestsDropdown.vue";
  import LeaveRequestsDropdownTypes from "./LeaveRequestsDropdownTypes.vue";
  import { FormInput } from "@starter-core/dash-ui/src";
  import FlatPickr from 'vue-flatpickr-component'
  import 'flatpickr/dist/flatpickr.css'
import { UserRecord } from "../types";

  const { t } = useI18n();
  const leaveTypes = ref([]);
  const managers = ref([]);
  const admins = ref([]);
  const users = ref([]);
  const userId = defineModel<number>("userId", { default: 0 });

  const leaveTypeId = defineModel<number | null>("leaveTypeId", { default: null });
  const requestTo = defineModel<number | null>("requestTo", { default: null });

  const startDate = defineModel<string>("startDate", { default: "" });
  const endDate = defineModel<string>("endDate", { default: "" });
  const reason = defineModel<string>("reason", { default: "" });


  const today = new Date();
  const currentYear = today.getFullYear();

  const minDate = computed(() => today.toISOString().split("T")[0]);
  const maxDate = computed(() => `${currentYear + 1}-12-31`);

  const props = defineProps(["user", "isEditPage", "requesterName"]);

  const fetchLeaveTypes = async () => {
    try {
      const response = await axios.get("/leave_type/all");
      leaveTypes.value = response.data;
    } catch (error) {
      console.error("Error fetching leave types:", error);
    }
  };

  const fetchManagers = async () => {
    try {
      const response = await axios.get("/user/draw", {
        params: { search: "manager" },
      });
      managers.value = response.data.data;
    } catch (error) {
      console.error("Error fetching managers:", error);
    }
  };

  const fetchAdmins = async () => {
    try {
      const response = await axios.get("/user/draw", {
        params: { search: "admin" },
      });
      admins.value = response.data.data;
    } catch (error) {
      console.error("Error fetching admins:", error);
    }
  };

  const fetchAllUsers = async () => {
    try {
      const response = await axios.get("/user/draw");
      users.value = response.data.data.filter(
        (u: any) => u.role !== 3 && u.role !== 4
      );
    } catch (error) {
      console.error("Error fetching admins:", error);
    }
  };

  onMounted(() => {
    fetchLeaveTypes();
    fetchAdmins();
    fetchManagers();
    fetchAllUsers();
  });

  const baseConfig = computed(() => ({
  dateFormat: 'Y-m-d',   // value bound to v-model
  altInput: true,
  altFormat: 'd/m/Y',    // what the user sees
  locale: { firstDayOfWeek: 1 }, // Monday
  allowInput: true,
  }))

  const startConfig = computed(() => ({
    ...baseConfig.value,
    maxDate: maxDate.value,
  }))

  const endConfig = computed(() => ({
    ...baseConfig.value,
    minDate: startDate.value || minDate.value,
    maxDate: maxDate.value,
  }))
</script>
<template>
  <div class="kt-section">
    <div class="kt-section__body">
        <div class="form-group requester" v-if="props.requesterName && isEditPage">
          <label class="form-group__label">Requested by:</label>
          <div class="requester__name">{{ props.requesterName ? props.requesterName : (props.user.first_name + ' ' + props.user.last_name) }}</div>
        </div>
      <leave-requests-dropdown
        v-if="isEditPage"
        v-model:model="requestTo"
        :optionsData="users"
        :readonly="
          userId == props.user.id ||
          requestTo == props.user.id ||
          props.user.role == 1
            ? false
            : true
        "
      />

      <leave-requests-dropdown
        v-else-if="user.role == 1 || user.role == 2"
        v-model:model="requestTo"
        :optionsData="admins"
        :readonly="false"
      />
      <leave-requests-dropdown
        v-else
        v-model:model="requestTo"
        :optionsData="managers"
        :readonly="false"
      />

      <leave-requests-dropdown-types
        v-model:model="leaveTypeId"
        :optionsData="leaveTypes"
        :readonly="false"
      />

      <form-input
        v-model="reason"
        name="reason"
        label="Reason (optional)"
        is-inline
      />
      <div class="dates_wrapper" lang="en-GB">
        <div class="dates_from" lang="en-GB">
          <label class="form-group__label" for="startDate">Start date:</label>
          <FlatPickr
            id="startDate"
            name="startDate"
            v-model="startDate"
            :config="startConfig"
            placeholder="dd/mm/yyyy"
          />
        </div>

        <div>
          <label class="form-group__label" for="endDate">End date:</label>
          <FlatPickr
            id="endDate"
            name="endDate"
            v-model="endDate"
            :config="endConfig"
            placeholder="dd/mm/yyyy"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">
  .dates_wrapper {
    display: flex;
    align-items: flex-start;

    .dates_from {
      margin-right: 100px;
    }
  }

  .requester {
    display: flex;
    align-items: center;
    margin-bottom: 12px;

    .form-group__label {
      font-weight: 600;
    }

    .requester__name {
      color: #333;
    }
  }
</style>
