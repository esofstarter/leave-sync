import type { RouteRecordRaw } from "vue-router";
import { USER_PERMISSIONS, COUNTRY_ROUTES_DATA } from "../constants";
import { i18n } from "@/plugins/i18n";
const { t } = i18n.global;

const Country = () =>
  import(
    /* webpackChunkName: "countries" */
    /* webpackPrefetch: true */
    "../pages/CountryList.vue"
  );

const CountryPage = () =>
  import(
    /* webpackChunkName: "country-page" */
    /* webpackPrefetch: true */
    "../pages/CountryPage.vue"
  );

const { add, main, edit } = COUNTRY_ROUTES_DATA;

export const CountryRoutes: RouteRecordRaw[] = [
  {
    path: main.path,
    name: main.name,
    component: Country,
    meta: {
      auth: {
        roles: [USER_PERMISSIONS.readUsers],
      },
    },
  },
  {
    path: add.path,
    name: add.name,
    component: CountryPage,
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
    component: CountryPage,
    meta: {
      title: t(edit.translationKey, null),
      auth: {
        roles: [USER_PERMISSIONS.writeUsers],
      },
    },
  },
];
