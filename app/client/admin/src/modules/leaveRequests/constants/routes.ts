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
      translationKey: "leave_requests",
    },
    all: {
      path: "leave_requests_all",
      name: "leave_requests_all",
      translationKey: "leave_requests",
    },
    add: {
      path: "leave_request/add",
      name: "add.leave_request",
      translationKey: "leave_requests.add",
    },
    edit: {
      path: "leave_request/:leaveRequestId",
      name: "edit.leave_request",
      translationKey: "leave_requests.edit_leave_requests",
    },
    approve: {
      path: "leave_request/:leaveRequestId/confirmation",
      name: "approve.leave_request",
      translationKey: "leave_requests.approve_leave_requests",
    },
    vacationDays: {
      path: "vacation_days",
      name: "vacation_days",
      translationKey: "vacation_days",
    },
  };
