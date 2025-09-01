<script lang="ts" setup>
  import { IconSave, IconArrowleft } from "@starter-core/icons";
  import { useAuth } from "@websanova/vue-auth/src/v3.js";
  import { useForm } from "vee-validate";
  import { watch, computed, onMounted } from "vue";
  import { useI18n } from "vue-i18n";
  import { useRoute } from "vue-router";
  import {
    TabbedContent,
    TabbedContentTab,
    PageWrapper,
    PAGE_WRAPPER_SLOTS,
    SubheaderTitle,
  } from "../../../components";
  import {
    LeaveRequestsFormBasicInfoTab,
    LeaveRequestsFormApproveTab,
  } from "../components";
  import { useLeaveRequestsForm } from "../composables";
  import type { LeaveRequestFormItem } from "../types";
  import { useRootStore } from "@/store/root";
  import { DashButton, DashLink } from "@starter-core/dash-ui/src";

  const { t } = useI18n();
  const basicInfoLabeel = t("users.basic.information");
  const route = useRoute();
  const isEditPage = computed(() => route.name == "edit.leave_request");
  const isApprovePage = computed(() => route.name == "approve.leave_request");
  const leaveRequestId = Number(route.params.leaveRequestId);
  const { setBackUrl } = useRootStore();

  const auth = useAuth();
  const myUser = auth.user();

  const {
    isLoading,
    data: formData,
    createLeaveRequest,
    updateLeaveRequest,
    approveLeaveRequest,
    approveUpdateLeaveRequest,
    declineLeaveRequest,
  } = useLeaveRequestsForm(leaveRequestId);

  const { handleSubmit, errors, setValues, defineField } =
    useForm<LeaveRequestFormItem>({});

  const submitHandler = handleSubmit((values) => {
    if (isEditPage.value) {
      updateLeaveRequest(values);
    } else {
      createLeaveRequest(values);
    }
  });

  const approveUpdate = handleSubmit((values) => {
    approveUpdateLeaveRequest(values);
  });

  const approve = handleSubmit((values) => {
    approveLeaveRequest(values);
  });

  const decline = handleSubmit((values) => {
    declineLeaveRequest(values);
  });

  onMounted(() => {
    auth.fetch();
    if (myUser && myUser.id) {
      userId.value = myUser.id;
    }
  });
  const formatDate = (date: string | Date | null): string => {
    if (!date) return "";
    return new Date(date).toISOString().split("T")[0]; // Extracts only YYYY-MM-DD
  };

  watch(() => {
    if (formData.value) {
      setValues({
        id: formData.value.id,
        user_id: formData.value.user_id,
        leave_type_id: formData.value.leave_type_id,
        start_date: formatDate(formData.value.start_date),
        end_date: formatDate(formData.value.end_date),
        reason: formData.value.reason,
        request_to: formData.value.request_to,
        is_confirmed: formData.value.is_confirmed,
      });
    }
  }, [formData]);

  const [userId] = defineField("user_id");
  const [leaveTypeId] = defineField("leave_type_id");
  const [startDate] = defineField("start_date");
  const [endDate] = defineField("end_date");
  const [reason] = defineField("reason");
  const [requestTo] = defineField("request_to");
  const [isConfirmed] = defineField("is_confirmed");

  const isUserAllowedToUpdate = computed(
    () =>
      !(
        isConfirmed.value == 2 &&
        (auth.user().id !== userId.value || auth.user().id !== requestTo.value)
      ),
  );
</script>

<template>
  <PageWrapper>
    <template #[PAGE_WRAPPER_SLOTS.subheaderMain]>
      <SubheaderTitle title="Request" />
    </template>
    <template v-if="!isApprovePage" #[PAGE_WRAPPER_SLOTS.subheaderToolbox]>
      <DashLink
        v-if="!isApprovePage"
        to="/admin/leave_requests"
        :icon="IconArrowleft"
        theme="clean"
      >
        {{ t("buttons.back") }}
      </DashLink>
      <DashButton
        v-if="isUserAllowedToUpdate"
        type="submit"
        :icon="IconSave"
        :loading="isLoading"
        @click="submitHandler"
      >
        {{ t("buttons.save") }}
      </DashButton>
    </template>
    <form
      autocomplete="off"
      enctype="multipart/form-data"
      @submit.prevent="submitHandler"
    >
      <TabbedContent :isLoading="isLoading">
        <TabbedContentTab
          v-if="!isApprovePage"
          :label="basicInfoLabeel"
          id="basic-info"
        >
          <LeaveRequestsFormBasicInfoTab
            v-model:userId="userId"
            v-model:leaveTypeId="leaveTypeId"
            v-model:startDate="startDate"
            v-model:endDate="endDate"
            v-model:reason="reason"
            v-model:requestTo="requestTo"
            :errors="errors"
            :user="myUser"
            :isEditPage="isEditPage"
          />
        </TabbedContentTab>
        <TabbedContentTab
          v-else
          :label="'Leave Request Confirmation'"
          id="basic-info"
        >
          <LeaveRequestsFormApproveTab
            v-model:userId="userId"
            v-model:leaveTypeId="leaveTypeId"
            v-model:startDate="startDate"
            v-model:endDate="endDate"
            v-model:reason="reason"
            v-model:requestTo="requestTo"
            :user="auth.user()"
          />
        </TabbedContentTab>
        <div
          v-if="
            auth.user().id == requestTo ||
            (isConfirmed == 2 && auth.user().role == 1)
          "
        >
          <div v-if="isConfirmed == 0 && isApprovePage">
            <div class="confirmation_btn_wrapper">
              <span class="req_btn approve" @click="approve"> Approve </span>
              <span class="req_btn decline" @click="decline"> Decline </span>
            </div>
          </div>
          <!-- <div v-else-if="isConfirmed == 2 && (auth.user().role == 1 || (auth.user().role == 2 && auth.user().id == requestTo))"> -->
          <div v-else-if="isConfirmed == 2 && auth.user().role == 1">
            <div class="confirmation_btn_wrapper">
              <span class="req_btn approve" @click="approveUpdate">
                Update
              </span>
            </div>
          </div>
        </div>
      </TabbedContent>
    </form>
  </PageWrapper>
</template>
<style scoped lang="scss">
  .confirmation_btn_wrapper {
    display: flex;
  }
  .req_btn {
    color: white;
    font-size: 12px;
    font-weight: bold;
    padding: 10px 15px;

    &:hover {
      cursor: pointer;
    }
  }

  .approve {
    border-radius: 4px;
    background: #4eab58;
    color: white;
    margin-right: 8px;
    &:hover {
      background: #46a750;
    }
  }

  .decline {
    border-radius: 4px;
    background: #d9534f;
    color: white;

    &:hover {
      background: #c9302c;
    }
  }

  .spinner {
    display: inline-block;
    width: 14px;
    height: 14px;
    border: 2px solid currentColor;
    border-top-color: transparent;
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
  }

  @keyframes spin {
    100% {
      transform: rotate(360deg);
    }
  }
</style>
