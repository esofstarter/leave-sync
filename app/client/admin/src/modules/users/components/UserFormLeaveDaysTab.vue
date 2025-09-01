<script lang="ts" setup>
  import { onMounted } from "vue";
  import { useI18n } from "vue-i18n";
  import { FormInput } from "@starter-core/dash-ui/src";

  const { t } = useI18n();
  const paidLeavesMax = defineModel("paidLeavesMax", {
    required: true,
    type: Number,
  });
  const props = defineProps(["daysLeft", "isMyProfile"]);

  onMounted(() => {});
</script>
<template>
  <div class="kt-section">
    <div class="kt-section__body">
      <h3 class="kt-section__title kt-section__title-lg">Leave Days:</h3>
      <form-input
        v-if="!props.isMyProfile"
        name="paidLeavesMax"
        type="text"
        :label="'Leave Types (by Year)'"
        v-model="paidLeavesMax"
        is-inline
      />
      <span>
        Paid Days Left: <strong>{{ props.daysLeft }}</strong>
      </span>
    </div>
  </div>
</template>
