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
  import { CountryFormBasicInfoTab } from "../components";
  import { useCountriesForm } from "../composables";
  import type { CountryFormItem } from "../types";
  import { DashButton, DashLink } from "@starter-core/dash-ui/src";

  const { t } = useI18n();
  const basicInfoLabeel = t("users.basic.information");
  const route = useRoute();
  const isEditPage = computed(() => route.name == "edit.country");
  const countryId = Number(route.params.countryId);

  const {
    isLoading,
    data: formData,
    createCountry,
    updateCountry,
  } = useCountriesForm(countryId);

  const { handleSubmit, errors, setValues, defineField } =
    useForm<CountryFormItem>();

  const submitHandler = handleSubmit((values) => {
    if (isEditPage.value) {
      updateCountry(values);
    } else {
      createCountry(values);
    }
  });

  watch(() => {
    if (formData.value) {
      setValues({
        id: formData.value.id,
        country_code: formData.value.country_code,
        name: formData.value.name,
      });
    }
  }, [formData]);

  const [countryCode] = defineField("country_code");
  const [name] = defineField("name");
</script>

<template>
  <PageWrapper>
    <template #[PAGE_WRAPPER_SLOTS.subheaderMain]>
      <SubheaderTitle title="Country Page" />
    </template>
    <template #[PAGE_WRAPPER_SLOTS.subheaderToolbox]>
      <DashLink to="/admin/countries" :icon="IconArrowleft" theme="clean">
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
          <CountryFormBasicInfoTab
            v-model:name="name"
            v-model:countryCode="countryCode"
            :errors="errors"
          />
        </TabbedContentTab>
      </TabbedContent>
    </form>
  </PageWrapper>
</template>
