import type { ModulesRoutesData } from "@/types/routes";

export const LEAVE_REQUESTS_ROUTES = {
  main: "main",
  all: "all",
  add: "add",
  edit: "edit",
  approve: "approve",
  vacationDays: "vacationDays",
} as const;

type LeaveRequestsRoutes =
  (typeof LEAVE_REQUESTS_ROUTES)[keyof typeof LEAVE_REQUESTS_ROUTES];

export const LEAVE_REQUEST_ROUTES_DATA: ModulesRoutesData<LeaveRequestsRoutes> =
  {
    main: {
      path: "leave_requests",
      name: "leave_requests",
      translationKey: "admin.leave_requests.main",
    },
    all: {
      path: "leave_requests_all",
      name: "leave_requests_all",
      translationKey: "admin.leave_requests.all",
    },
    add: {
      path: "leave_request/add",
      name: "add.leave_request",
      translationKey: "admin.leave_requests.add",
    },
    edit: {
      path: "leave_request/:leaveRequestId",
      name: "edit.leave_request",
      translationKey: "admin.leave_requests.edit",
    },
    approve: {
      path: "leave_request/:leaveRequestId/confirmation",
      name: "approve.leave_request",
      translationKey: "admin.leave_requests.approve",
    },
    vacationDays: {
      path: "vacation_days",
      name: "vacation_days",
      translationKey: "admin.vacation_days.main",
    },
  };
