<script lang="ts" setup>
  import { IconSave, IconArrowleft } from "@starter-core/icons";
  import { useForm } from "vee-validate";
  import { watch, computed } from "vue";
  import { useI18n } from "vue-i18n";
  import { useRoute } from "vue-router";
  import {
    TabbedContent,
    TabbedContentTab,
    PageWrapper,
    PAGE_WRAPPER_SLOTS,
    SubheaderTitle,
  } from "../../../components";
  import { LeaveTypeFormBasicInfoTab } from "../components";
  import { useLeaveTypesForm } from "../composables";
  import type { LeaveTypeFormItem } from "../types";
  import { DashButton, DashLink } from "@starter-core/dash-ui/src";

  const { t } = useI18n();
  const basicInfoLabeel = t("users.basic.information");
  const route = useRoute();
  const isEditPage = computed(() => route.name == "edit.leave_type");
  const leaveTypeId = Number(route.params.leaveTypeId);

  const {
    isLoading,
    data: formData,
    createLeaveType,
    updateLeaveType,
  } = useLeaveTypesForm(leaveTypeId);

  const { handleSubmit, errors, setValues, defineField } =
    useForm<LeaveTypeFormItem>();

  const submitHandler = handleSubmit((values) => {
    if (isEditPage.value) {
      updateLeaveType(values);
    } else {
      createLeaveType(values);
    }
  });

  watch(() => {
    if (formData.value) {
      setValues({
        id: formData.value.id,
        slug: formData.value.slug,
        name: formData.value.name,
        is_paid: formData.value.is_paid,
        color: formData.value.color,
      });
    }
  }, [formData]);

  const [slug] = defineField("slug");
  const [name] = defineField("name");
  const [isPaid] = defineField("is_paid");
  const [color] = defineField("color");
</script>

<template>
  <PageWrapper>
    <template #[PAGE_WRAPPER_SLOTS.subheaderMain]>
      <SubheaderTitle title="Leave Type" />
    </template>
    <template #[PAGE_WRAPPER_SLOTS.subheaderToolbox]>
      <DashLink to="/admin/leave_types" :icon="IconArrowleft" theme="clean">
        {{ t("buttons.back") }}
      </DashLink>
      <DashButton
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
        <TabbedContentTab :label="basicInfoLabeel" id="basic-info">
          <LeaveTypeFormBasicInfoTab
            v-model:isPaid="isPaid"
            v-model:name="name"
            v-model:slug="slug"
            v-model:color="color"
            :errors="errors"
          />
        </TabbedContentTab>
      </TabbedContent>
    </form>
  </PageWrapper>
</template>
