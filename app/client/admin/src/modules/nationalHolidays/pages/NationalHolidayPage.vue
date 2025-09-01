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
  import { NationalHolidayFormBasicInfoTab } from "../components";
  import { useNationalHolidaysForm } from "../composables";
  import type { NationalHolidayFormItem } from "../types";
  import { DashButton, DashLink } from "@starter-core/dash-ui/src";

  const { t } = useI18n();
  const basicInfoLabeel = t("users.basic.information");
  const route = useRoute();
  const isEditPage = computed(() => route.name == "edit.national_holiday");
  const nationalHolidayId = Number(route.params.nationalHolidayId);

  const {
    isLoading,
    data: formData,
    createNationalHoliday,
    updateNationalHoliday,
  } = useNationalHolidaysForm(nationalHolidayId);

  const { handleSubmit, errors, setValues, defineField } =
    useForm<NationalHolidayFormItem>();

  const submitHandler = handleSubmit((values) => {
    if (isEditPage.value) {
      updateNationalHoliday(values);
    } else {
      createNationalHoliday(values);
    }
  });

  watch(() => {
    if (formData.value) {
      setValues({
        id: formData.value.id,
        date: formData.value.date,
        country: formData.value.country,
        year: formData.value.year,
      });
    }
  }, [formData]);

  
  const [date] = defineField("date");
  const [country] = defineField("country");
  const [year] = defineField("year");
</script>

<template>
  <PageWrapper>
    <template #[PAGE_WRAPPER_SLOTS.subheaderMain]>
      <SubheaderTitle title="National Holiday" />
    </template>
    <template #[PAGE_WRAPPER_SLOTS.subheaderToolbox]>
      <DashLink to="/admin/national_holidays" :icon="IconArrowleft" theme="clean">
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
          <NationalHolidayFormBasicInfoTab
            v-model:date="date"
            v-model:country="country"
            v-model:year="year"
            :errors="errors"
          />
        </TabbedContentTab>
      </TabbedContent>
    </form>
  </PageWrapper>
</template>
