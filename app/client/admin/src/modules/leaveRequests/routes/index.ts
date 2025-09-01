import type { RouteRecordRaw } from "vue-router";
import { USER_PERMISSIONS, LEAVE_REQUEST_ROUTES_DATA } from "../constants";
import { i18n } from "@/plugins/i18n";
const { t } = i18n.global;

const LeaveRequests = () =>
  import(
    /* webpackChunkName: "users" */
    /* webpackPrefetch: true */
    "../pages/LeaveRequestsList.vue"
  );

const LeavesCalendar = () =>
  import(
    /* webpackChunkName: "users" */
    /* webpackPrefetch: true */
    "../pages/LeaveCalendarPage.vue"
  );

const LeaveRequestPage = () =>
  import(
    /* webpackChunkName: "user-page" */
    /* webpackPrefetch: true */
    "../pages/LeaveRequestPage.vue"
  );

const { add, main, all, edit, approve, vacationDays } = LEAVE_REQUEST_ROUTES_DATA;

export const LeaveRequestsRoutes: RouteRecordRaw[] = [
  {
    path: main.path,
    name: main.name,
    component: LeaveRequests,
    meta: {
      auth: {
        roles: [USER_PERMISSIONS.readRequests],
      },
    },
  },
  {
    path: all.path,
    name: all.name,
    component: LeaveRequests,
    meta: {
      auth: {
        roles: [USER_PERMISSIONS.readRequests],
      },
    },
  },
  {
    path: add.path,
    name: add.name,
    component: LeaveRequestPage,
    meta: {
      title: t(add.translationKey, null),
      auth: {
        roles: [USER_PERMISSIONS.writeRequests],
      },
    },
  },
  {
    path: edit.path,
    name: edit.name,
    component: LeaveRequestPage,
    meta: {
      title: t(edit.translationKey, null),
      auth: {
        roles: [USER_PERMISSIONS.writeRequests],
      },
    },
  },
  {
    path: approve.path,
    name: approve.name,
    component: LeaveRequestPage,
    meta: {
      title: t(edit.translationKey, null),
      auth: {
        roles: [USER_PERMISSIONS.approveRequests],
      },
    },
  },
  {
    path: vacationDays.path,
    name: vacationDays.name,
    component: LeavesCalendar,
    meta: {
      auth: {
        roles: [USER_PERMISSIONS.readRequests],
      },
    },
  },
];
