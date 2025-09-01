<script lang="ts" setup>
  import { IconMail } from "@starter-core/icons";
  import { useI18n } from "vue-i18n";
  import { FormInput } from "@starter-core/dash-ui/src";

  const { t } = useI18n();
  const countryCode = defineModel("countryCode", {
    required: true,
    type: String,
  });
  const name = defineModel("name", { required: true, type: String });
</script>
<template>
  <div class="kt-section">
    <div class="kt-section__body">
      <form-input
        v-model="countryCode"
        name="countryCode"
        label="Country Code"
        is-inline
      />
      <form-input v-model="name" name="name" label="Name" is-inline />
    </div>
  </div>
</template>
