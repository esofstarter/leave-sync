<script lang="ts" setup>
  import { IconMail } from "@starter-core/icons";
  import { useI18n } from "vue-i18n";
  import { FormInput, FormSwitch } from "@starter-core/dash-ui/src";

  const { t } = useI18n();
  const slug = defineModel("slug", { required: true, type: String });
  const name = defineModel("name", { required: true, type: String });
  const isPaid = defineModel("isPaid", { required: true, type: Boolean });
  const color = defineModel("color", { required: true, type: String });
</script>
<template>
  <div class="kt-section">
    <div class="kt-section__body">
      <form-switch
        v-model="isPaid"
        id="enabled"
        :theme="isPaid ? 'success' : 'danger'"
        type="outline"
        label="Paid"
        :helper-text="`Leave Type is  ${isPaid ? 'Paid' : 'Unpaid'}`"
      />
      <form-input v-model="slug" name="slug" label="Slug" is-inline />
      <form-input v-model="name" name="name" label="Name" is-inline />
      <div class="form-group form-input form-group--inline">
        <div
          class="form-group__column form-group__column--left form-group__column--inline"
        >
          <label class="form-group__label" for="name">Select Color</label>
        </div>
        <div
          class="form-group__column form-group__column--right form-group__column--inline"
        >
          <input
            type="color"
            id="colorPicker"
            v-model="color"
            class="color-input"
          />
        </div>
      </div>
    </div>
  </div>
</template>
<style scoped>
  .color-picker {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 10px;
  }

  .color-input {
    border: none;
    padding: 0;
    background: transparent;
    cursor: pointer;
    width: 40px;
    height: 40px;
  }

  .color-preview {
    width: 40px;
    height: 40px;
    border: 1px solid #ccc;
    border-radius: 4px;
  }
</style>
