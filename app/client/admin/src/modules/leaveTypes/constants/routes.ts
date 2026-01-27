import type { ModulesRoutesData } from "@/types/routes";

export const LEAVE_TYPES_ROUTES = {
  main: "main",
  add: "add",
  edit: "edit",
} as const;

type LeaveTypesRoutes =
  (typeof LEAVE_TYPES_ROUTES)[keyof typeof LEAVE_TYPES_ROUTES];

export const LEAVE_TYPE_ROUTES_DATA: ModulesRoutesData<LeaveTypesRoutes> = {
  main: {
    path: "leave_types",
    name: "leave_types",
    translationKey: "admin.leave_types.main",
  },
  add: {
    path: "leave_type/add",
    name: "add.leave_type",
    translationKey: "admin.leave_types.add",
  },
  edit: {
    path: "leave_type/:leaveTypeId",
    name: "edit.leave_type",
    translationKey: "admin.leave_types.edit",
  },
};
