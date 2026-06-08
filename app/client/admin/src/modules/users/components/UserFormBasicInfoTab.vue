<script lang="ts" setup>
  import { IconMail } from "@starter-core/icons";
  import { ref, computed } from "vue";
  import { useI18n } from "vue-i18n";
  import UserCountriesDropdown from "./UserCountriesDropdown.vue";
  import UserFormAvatar from "./UserFormAvatar.vue";
  import UserRolesDropdown from "./UserRolesDropdown.vue";
  import { FormInput, FormSwitch } from "@starter-core/dash-ui/src";

  type EmitsType = {
    (event: "uploadAvatar", file: File): void;
  };

  const { t } = useI18n();
  const isDisabled = defineModel<boolean>("isDisabled", { default: false });
  const isOfficeBased = defineModel<boolean>("isOfficeBased", { default: false });

  const role = defineModel<number>("role", { default: 0 });
  const lastName = defineModel<string>("lastName", { default: "" });
  const firstName = defineModel<string>("firstName", { default: "" });
  const country = defineModel<number>("country", { default: 0 });
  const email = defineModel<string>("email", { default: "" });

  const privateId = defineModel<string | null>("privateId", { default: null });
  const position = defineModel<string | null>("position", { default: null });
  const password = defineModel("password", { required: true });
  const passwordConfirmation = defineModel<string>("passwordConfirmation", { default: "" });
  const confirmPassword = ref(""); // Store confirm password field

  const props = withDefaults(defineProps<{
    errors?: any;
    avatar?: string | null;
    paidLeavesLeft?: number;
    isEdit: boolean;
    isMyProfile: boolean;
  }>(), {
    avatar: null,
    errors: () => ({}),
    paidLeavesLeft: 0,
  });

  const emit = defineEmits<EmitsType>();

  const uploadAvatar = (file: File) => {
    emit("uploadAvatar", file);
  };
  const passwordError = computed(() => {
    if (confirmPassword.value && confirmPassword.value !== password.value) {
      return 'Passwords do not match!';
    }
    return "";
  });

  const passwordLabel = computed(() => {
    if (props.isEdit) {
      return 'New Password';
    }
    return "Password";
  });
</script>
<template>
  <div  v-if="!isMyProfile" class="kt-section kt-section--first">
    <div class="kt-section__body">
      <h3 class="kt-section__title kt-section__title-lg">
        {{ t("users.user_status") }}:
      </h3>
      <user-roles-dropdown v-model:role="role" />

      <UserCountriesDropdown v-model:country="country" />

      <form-switch
        v-model="isDisabled"
        id="enabled"
        theme="danger"
        type="outline"
        :label="t('users.status.label')"
        :helper-text="`User is  ${isDisabled ? 'disabled' : 'enabled'}`"
      />
      <form-switch
        v-model="isOfficeBased"
        id="enabled"
        theme="danger"
        type="outline"
        :label="'Office Based'"
        :helper-text="`User is  ${isOfficeBased ? '' : 'not'} Office Based`"
      />
    </div>
  </div>

  <div
    v-if="!isMyProfile"
    class="kt-separator kt-separator--border-dashed kt-separator--space-lg"
  ></div>

  <div class="kt-section">
    <div class="kt-section__body">
      <h3 class="kt-section__title kt-section__title-lg">User Info:</h3>
      <div v-if="isEdit" class="form-group form-input form-group--inline">
        <div
          class="form-group__column form-group__column--left form-group__column--inline"
        >
          <label class="form-group__label" for="avatar">{{
            t("users.avatar")
          }}</label>
        </div>
        <div
          class="form-group__column form-group__column--left form-group__column--inline"
        >
          <user-form-avatar
            :src="avatar"
            @change="uploadAvatar"
            is-circle
            is-outline
          />
        </div>
      </div>
      <form-input
        v-model="lastName"
        name="last-name"
        :label="t('users.last_name.label')"
        :error="errors.last_name"
        is-inline
      />
      <form-input
        v-model="firstName"
        name="first-name"
        :label="t('users.first_name.label')"
        :error="errors.first_name"
        is-inline
      />
      <form-input
        v-model="email"
        name="email"
        :label="t('users.email.label')"
        :error="errors.email"
        is-inline
      >
        <template v-slot:prependContent>
          <IconMail />
        </template>
      </form-input>
      <form-input
        name="password"
        type="password"
        :label="passwordLabel"
        v-model="password"
        :error="errors.password"
        is-inline
      />

      <form-input
        name="password_confirmation"
        type="password"
        label="Confirm Password"
        v-model="passwordConfirmation"
        :error="errors.password_confirmation"
        is-inline
    />
      <form-input
        v-if="isMyProfile"
        v-model="privateId"
        name="private-id"
        :label="t('users.privateId.label')"
        :error="errors.private_id"
        is-inline
      />
      <form-input
        v-model="position"
        name="position"
        :label="t('users.position.label')"
        :error="errors.position"
        is-inline
      />
    </div>
  </div>
</template>
