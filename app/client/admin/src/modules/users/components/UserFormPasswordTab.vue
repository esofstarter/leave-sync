<script lang="ts" setup>
import { ref, computed, defineProps } from "vue";
import { useI18n } from "vue-i18n";
import { FormInput } from "@starter-core/dash-ui/src";

const { t } = useI18n();
const password = defineModel("password", { required: true });
const confirmPassword = ref(""); // Store confirm password field

const props = defineProps(["isEdit"]);

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
  <div class="kt-section">
    <div class="kt-section__body">
      <h3 class="kt-section__title kt-section__title-lg">
        {{ t("users.password.new_password") }}:
      </h3>

      <form-input
        name="password"
        type="password"
        :label="passwordLabel"
        v-model="password"
        is-inline
      />

      <form-input
        name="confirm-password"
        type="password"
        :label="t('users.password.confirm')"
        v-model="confirmPassword"
        is-inline
        :error="passwordError"
      />
    </div>
  </div>
</template>

<style scoped>
.error-message {
  color: red;
  font-size: 0.9rem;
  margin-top: 5px;
}
</style>
