import type { RouteRecordRaw } from "vue-router";
import { USER_PERMISSIONS, NATIONAL_HOLIDAY_ROUTES_DATA } from "../constants";
import { i18n } from "@/plugins/i18n";
const { t } = i18n.global;

const NationalHolidaysList = () =>
  import(
    /* webpackChunkName: "national_holidays" */
    /* webpackPrefetch: true */
    "../pages/NationalHolidaysList.vue"
  );

const nationalHolidayPage = () =>
  import(
    /* webpackChunkName: "national-holiday-page" */
    /* webpackPrefetch: true */
    "../pages/NationalHolidayPage.vue"
  );

const { add, main, edit } = NATIONAL_HOLIDAY_ROUTES_DATA;

export const NationalHolidaysRoutes: RouteRecordRaw[] = [
  {
    path: main.path,
    name: main.name,
    component: NationalHolidaysList,
    meta: {
      auth: {
        roles: [USER_PERMISSIONS.readUsers],
      },
    },
  },
  {
    path: add.path,
    name: add.name,
    component: nationalHolidayPage,
    meta: {
      title: t(add.translationKey, null),
      auth: {
        roles: [USER_PERMISSIONS.writeUsers],
      },
    },
  },
  {
    path: edit.path,
    name: edit.name,
    component: nationalHolidayPage,
    meta: {
      title: t(edit.translationKey, null),
      auth: {
        roles: [USER_PERMISSIONS.writeUsers],
      },
    },
  },
];
