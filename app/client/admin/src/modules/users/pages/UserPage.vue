<script lang="ts" setup>
  import { IconSave, IconArrowleft } from "@starter-core/icons";
  import { useAuth } from "@websanova/vue-auth/src/v3.js";
  import { useForm } from "vee-validate";
  import { watch, computed, ref } from "vue";
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
    UserFormBasicInfoTab,
    UserFormPasswordTab,
    UserFormCalendarTab,
    UserFormLeaveDaysTab,
    UserFormDocumentsTab,
  } from "../components";
  import { useUsersForm } from "../composables";
  import type { UserFormItem } from "../types";
  import { DashButton, DashLink } from "@starter-core/dash-ui/src";
  import ConfirmDialog from "@/components/ConfirmDialog/ConfirmDialog.vue";

  const { t } = useI18n();
  const basicInfoLabeel = t("users.basic.information");
  const changePasswordLabel = t("users.password.change");
  const route = useRoute();
  const isEditPage = computed(() => route.name == "edit.user");
  const userId = Number(route.params.userId);
  const newFile = ref<File | null>(null);
  const showConfirmModal = ref(false);
  const wasPaidLeavesMaxChanged = ref(false);
  const originalPaidLeavesMax = ref<number | null>(null);

  const auth = useAuth();
  const isUserWriter = auth.user().permissions_array.includes("write_users")
    ? true
    : false;

  const validationSchema = {
    last_name(value: string) {
      if (value?.length >= 5) return true;
      return "Last name needs to be at least 5 characters.";
    },
    first_name(value: string) {
      if (value?.length >= 5) return true;
      return "First name needs to be at least 5 characters.";
    },
    role(value: number) {
      if (value > 0) return true;
      return "Name needs to be at least 5 characters.";
    },
    email(value: string) {
      if (value?.length >= 5) return true;
      return "Name needs to be at least 5 characters.";
    },
  };

  const {
    isLoading,
    data: formData,
    createUser,
    updateUser,
    uploadAvatar,
  } = useUsersForm(userId);

  const { handleSubmit, errors, setValues, defineField } =
    useForm<UserFormItem>({
      validationSchema,
    });

  const submitHandler = handleSubmit((values) => {
    if (newFile.value !== null) {
      uploadAvatar(newFile.value);
    }
    if (isEditPage.value) {
      updateUser(values);
    } else {
      createUser(values);
    }
  });

  const uploadAvatarHandler = (file: File) => {
    newFile.value = file;
  };

  watch(() => {
    if (formData.value) {
      setValues({
        id: formData.value.id,
        email: formData.value.email,
        first_name: formData.value.first_name,
        last_name: formData.value.last_name,
        role: formData.value.role,
        is_disabled: formData.value.is_disabled,
        paid_leaves_max: formData.value.paid_leaves_max,
        paid_leaves_left: formData.value.paid_leaves_left,
        country: formData.value.country,
        is_office_based: formData.value.is_office_based,
      });

      originalPaidLeavesMax.value = formData.value.paid_leaves_max;
    }
  }, [formData]);

  const [id] = defineField("id");
  const [lastName] = defineField("last_name");
  const [firstName] = defineField("first_name");
  const [email] = defineField("email");
  const [isDisabled] = defineField("is_disabled");
  const [role] = defineField("role");
  const [password] = defineField("password");
  const [paidLeavesMax] = defineField("paid_leaves_max");
  const [paidLeavesLeft] = defineField("paid_leaves_left");
  const [country] = defineField("country");
  const [isOfficeBased] = defineField("is_office_based");

  watch(formData, () => {
    if (formData.value) {
      originalPaidLeavesMax.value = formData.value.paid_leaves_max;
      setValues({
        ...formData.value
      });
    }
  });

  watch(paidLeavesMax, (newVal) => {
    if (originalPaidLeavesMax.value !== null) {
      wasPaidLeavesMaxChanged.value = newVal !== originalPaidLeavesMax.value;
    }
  });

  const confirmAndSubmit = () => {
    if (wasPaidLeavesMaxChanged.value) {
      showConfirmModal.value = true;
    } else {
      submitHandler();
    }
  };

  const proceedSubmit = () => {
    showConfirmModal.value = false;
    submitHandler();
  };

  const confirmMessage = computed(() => {
    const currentUserId = auth.user().id;
    const isSelf = userId === currentUserId;

    if (isSelf) {
      return "Are you sure you want to update your own paid leave days?";
    }

    return `Are you sure you want to update the paid leave days for ${firstName.value} ${lastName.value}?`;
  });

</script>

<template>
  <PageWrapper>
    <template #[PAGE_WRAPPER_SLOTS.subheaderMain]>
      <SubheaderTitle
        :title="isEditPage ? 'Edit user' : 'Create User'"
        :description="isEditPage ? `${firstName} ${lastName}` : ''"
      />
    </template>
    <template #[PAGE_WRAPPER_SLOTS.subheaderToolbox]>
      <DashLink to="/admin/users" :icon="IconArrowleft" theme="clean">
        {{ t("buttons.back") }}
      </DashLink>
      <DashButton
        type="submit"
        :icon="IconSave"
        :loading="isLoading"
        @click="confirmAndSubmit"
      >
        {{ t("buttons.save") }}
      </DashButton>
    </template>
    <form
      autocomplete="off"
      enctype="multipart/form-data"
      @submit.prevent="confirmAndSubmit"
    >
      <TabbedContent :isLoading="isLoading">
        <TabbedContentTab :label="basicInfoLabeel" id="basic-info">
          <UserFormBasicInfoTab
            v-model:isDisabled="isDisabled"
            v-model:isOfficeBased="isOfficeBased"
            v-model:role="role"
            v-model:lastName="lastName"
            v-model:email="email"
            v-model:firstName="firstName"
            v-model:country="country"
            :paidLeavesLeft="paidLeavesLeft"
            :errors="errors"
            :avatar="formData?.avatar_thumbnail"
            :isEdit="isEditPage"
            :isMyProfile="false"
            @upload-avatar="uploadAvatarHandler"
          />
        </TabbedContentTab>
        <TabbedContentTab :label="'Password'" id="change-password">
          <UserFormPasswordTab v-model:password="password" :isEdit="isEditPage" />
        </TabbedContentTab>
        <TabbedContentTab
          v-if="isEditPage"
          :label="'Paid Vacation Days'"
          id="leave-days"
        >
          <UserFormLeaveDaysTab
            v-model:paidLeavesMax="paidLeavesMax"
            :daysLeft="paidLeavesLeft"
          />
        </TabbedContentTab>
        <TabbedContentTab v-if="isEditPage" :label="'Calednar'" id="calendar">
          <UserFormCalendarTab :userId="id" :country="country" />
        </TabbedContentTab>
        <TabbedContentTab
          v-if="isUserWriter && isEditPage"
          :label="'PDFs'"
          id="documents"
        >
          <UserFormDocumentsTab :userId="id" />
        </TabbedContentTab>
      </TabbedContent>
    </form>
  </PageWrapper>
  <ConfirmDialog
      :show="showConfirmModal"
      :message="confirmMessage"
      @confirm="proceedSubmit"
      @close="showConfirmModal = false"
    />
</template>
