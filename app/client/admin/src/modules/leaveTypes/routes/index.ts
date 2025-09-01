import type { RouteRecordRaw } from "vue-router";
import { USER_PERMISSIONS, LEAVE_TYPE_ROUTES_DATA } from "../constants";
import { i18n } from "@/plugins/i18n";
const { t } = i18n.global;

const LeaveTypes = () =>
  import(
    /* webpackChunkName: "users" */
    /* webpackPrefetch: true */
    "../pages/LeaveTypesList.vue"
  );

const LeaveTypePage = () =>
  import(
    /* webpackChunkName: "user-page" */
    /* webpackPrefetch: true */
    "../pages/LeaveTypePage.vue"
  );

const { add, main, edit } = LEAVE_TYPE_ROUTES_DATA;

export const LeaveTypesRoutes: RouteRecordRaw[] = [
  {
    path: main.path,
    name: main.name,
    component: LeaveTypes,
    meta: {
      auth: {
        roles: [USER_PERMISSIONS.readUsers],
      },
    },
  },
  {
    path: add.path,
    name: add.name,
    component: LeaveTypePage,
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
    component: LeaveTypePage,
    meta: {
      title: t(edit.translationKey, null),
      auth: {
        roles: [USER_PERMISSIONS.writeUsers],
      },
    },
  },
];
