export const LEAVE_TYPE_API_ENDPOINTS = {
  get: (leaveTypeId: number) => `/leave_type/${leaveTypeId}`,
  create: "/leave_type/create",
  patch: (leaveTypeId: number) => `/leave_type/${leaveTypeId}`,
  delete: (leaveTypeId: number) => `/leave_type/${leaveTypeId}/delete`,
  table: "leave_type/draw",
};

export const LEAVE_TYPES_QUERY_KEY = "leaveType-table";
