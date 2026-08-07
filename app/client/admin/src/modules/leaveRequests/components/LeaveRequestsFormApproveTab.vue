<script lang="ts" setup>
  import { useAuth } from "@websanova/vue-auth/src/v3.js";
  import axios from "axios";
  import { ref, onMounted, computed } from "vue";
  import { useI18n } from "vue-i18n";
  import LeaveRequestsDropdown from "./LeaveRequestsDropdown.vue";
  import LeaveRequestsDropdownTypes from "./LeaveRequestsDropdownTypes.vue";
  import FlatPickr from "vue-flatpickr-component";
  import "flatpickr/dist/flatpickr.css";
  
  const { t } = useI18n();
  const leaveTypes = ref([]);
  const managers = ref([]);
  const admins = ref([]);
  const userId = defineModel<number>("userId", { default: 0 });
  const leaveTypeId = defineModel<number | null>("leaveTypeId", { default: null });
  const startDate = defineModel<string>("startDate", { default: "" });
  const endDate = defineModel<string>("endDate", { default: "" });
  const reason = defineModel<string>("reason", { default: "" });
  const requestTo = defineModel<number | null>("requestTo", { default: null });

  const auth = useAuth();

  const props = defineProps(["user", "requesterName"]);

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

  const setUserOptions = computed(() => {
    return [...admins.value, ...managers.value];
  });


  const baseConfig = {
    dateFormat: "Y-m-d",        // bound to v-model
    altInput: true,
    altFormat: "d/m/Y",         // shown to user: 24.09.2025
    locale: { firstDayOfWeek: 1 },
    allowInput: false           // prevent manual typing
  };

  onMounted(() => {
    auth.fetch();
    fetchLeaveTypes();
    fetchManagers();
    fetchAdmins();
  });
</script>
<template>
  <div class="kt-section">
    <div class="kt-section__body">
      <div class="form-group requester" v-if="props.requesterName">
          <label class="form-group__label">Requested by:</label>
          <div class="requester__name">{{ props.requesterName ? props.requesterName : (props.user.first_name + ' ' + props.user.last_name) }}</div>
      </div>
      <leave-requests-dropdown
        class="noClick"
        v-model:model="requestTo"
        :optionsData="setUserOptions"
        :readonly="true"
      />

      <leave-requests-dropdown-types
        class="noClick"
        v-model:model="leaveTypeId"
        :optionsData="leaveTypes"
        :readonly="true"
      />

      <div class="form-group form-input form-group--inline" readonly>
        <div
          class="form-group__column form-group__column--left form-group__column--inline"
        >
          <label class="form-group__label" for="reason"
            >Reason (optional)</label
          >
        </div>
        <div
          class="form-group__column form-group__column--right form-group__column--inline"
        >
          <input
            name="reason"
            readonly
            class="form-input__input form-input__input--inline"
            type="text"
            v-model="reason"
          />
        </div>
      </div>

      <div class="dates_wrapper">
        <div class="dates_from">
          <label class="form-group__label" for="startDate">Start date:</label>
          <FlatPickr
            id="startDate"
            name="startDate"
            v-model="startDate"
            :config="baseConfig"
            placeholder="dd/mm/yyyy"
            disabled
          />
        </div>

        <div>
          <label class="form-group__label" for="endDate">End date:</label>
          <FlatPickr
            id="endDate"
            name="endDate"
            v-model="endDate"
            :config="baseConfig"
            placeholder="dd/mm/yyyy"
            disabled
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

  .noClick {
    pointer-events: none;
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
