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
    UserFormCalendarTab,
    UserFormLeaveDaysTab,
    UserFormDocumentsTab
  } from "../components";
  import { useUsersForm } from "../composables";
  import type { UserFormItem } from "../types";
  import { DashButton, DashLink } from "@starter-core/dash-ui/src";

  const { t } = useI18n();
  const basicInfoLabeel = t("users.basic.information");
  const changePasswordLabel = t("users.password.change");
  const route = useRoute();
  const auth = useAuth();

  const userId = Number(auth.user().id);
  const newFile = ref<File | null>(null);

  const isUserWriter = auth.user().permissions_array.includes("write_users")
    ? true
    : false;

  const {
    isLoading,
    data: formData,
    createUser,
    updateUser,
    uploadAvatar,
  } = useUsersForm(userId);
  const validationSchema = {
    last_name(value: string) {
      if (value?.length >= 3) return true;
      return "Last name needs to be at least 3 characters.";
    },
    first_name(value: string) {
      if (value?.length >= 3) return true;
      return "First name needs to be at least 3 characters.";
    },
    email(value: string) {
      if (value?.length >= 5) return true;
      return "Email needs to be at least 5 characters.";
    },

    // Password: only required when creating user OR when user typed something
    password(value: string) {
      if (!value) return true;

      if (value.length >= 8) return true;
      return "Password must be at least 8 characters.";
    },

    password_confirmation(value: string) {
      if (!password.value) return true;

      if (!value) return "Please confirm the password.";
      if (value === password.value) return true;

      return "Passwords do not match.";
    },
  };
  const { handleSubmit, errors, setValues, defineField } = useForm<UserFormItem>({
    validationSchema,
    initialValues: {
      id: 0,
      first_name: "",
      last_name: "",
      email: "",
      role: 0,
      is_disabled: false,
      is_office_based: false,
      paid_leaves_max: 0,
      paid_leaves_left: 0,
      country: 1,
      private_id: "",
      position: "",
      password: "",
      password_confirmation: "",
    },
  });


  const submitHandler = handleSubmit((values) => {
    updateUser(values);
    if (newFile.value !== null) {
      uploadAvatar(newFile.value);
    }
  });

  const uploadAvatarHandler = (file: File) => {
    newFile.value = file;
  };

  watch(formData, (value) => {
    if (value) {
      setValues({
        id: value.id,
        email: value.email,
        first_name: value.first_name,
        last_name: value.last_name,
        role: value.role,
        is_disabled: value.is_disabled,
        paid_leaves_max: value.paid_leaves_max,
        paid_leaves_left: value.paid_leaves_left,
        country: value.country,
        is_office_based: value.is_office_based,
        private_id: value.private_id,
        position: value.position,
        password: "",
        password_confirmation: "",
      });
    }
  });


  const [id] = defineField("id");
  const [lastName] = defineField("last_name");
  const [firstName] = defineField("first_name");
  const [email] = defineField("email");
  const [isDisabled] = defineField("is_disabled");
  const [role] = defineField("role");
  const [password] = defineField("password");
  const [passwordConfirmation] = defineField("password_confirmation");
  const [paidLeavesMax] = defineField("paid_leaves_max");
  const [paidLeavesLeft] = defineField("paid_leaves_left");
  const [country] = defineField("country");
  const [isOfficeBased] = defineField("is_office_based");
  const [privateId] = defineField("private_id");
  const [position] = defineField("position");
  
</script>

<template>
  <PageWrapper>
    <template #[PAGE_WRAPPER_SLOTS.subheaderMain]>
      <SubheaderTitle
        title="My Profile"
        :description="`${firstName} ${lastName}`"
      />
    </template>
    <template #[PAGE_WRAPPER_SLOTS.subheaderToolbox]>
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
          <UserFormBasicInfoTab
            v-model:isDisabled="isDisabled"
            v-model:isOfficeBased="isOfficeBased"
            v-model:role="role"
            v-model:lastName="lastName"
            v-model:email="email"
            v-model:firstName="firstName"
            v-model:country="country"
            v-model:privateId="privateId"
            v-model:position="position"
            v-model:password="password"
            v-model:password-confirmation="passwordConfirmation"
            :paidLeavesLeft="paidLeavesLeft"
            :isEdit="true"
            :isMyProfile="true"
            :errors="errors"
            :avatar="formData?.avatar_thumbnail"
            @upload-avatar="uploadAvatarHandler"
          />
        </TabbedContentTab>
        <TabbedContentTab v-if="auth.user().role !== 4" :label="'Paid Vacation Days'" id="leave-days">
          <UserFormLeaveDaysTab
            v-model:paidLeavesMax="paidLeavesMax"
            :daysLeft="paidLeavesLeft"
            :isMyProfile="true"
          />
        </TabbedContentTab>
        <TabbedContentTab v-if="auth.user().role !== 4" :label="'Calednar'" id="calendar">
          <UserFormCalendarTab :userId="id" :country="country" />
        </TabbedContentTab>
        <TabbedContentTab
          :label="'PDFs'"
          id="documents"
        >
          <UserFormDocumentsTab :userId="id" />
        </TabbedContentTab>
      </TabbedContent>
    </form>
  </PageWrapper>
</template>
