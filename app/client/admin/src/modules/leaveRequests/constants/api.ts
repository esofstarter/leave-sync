export const LEAVE_REQUEST_API_ENDPOINTS = {
  get: (leaveRequestId: number) => `/leave_request/${leaveRequestId}`,
  create: "/leave_request/create",
  list: "/leave_request/all",
  approve: (leaveRequestId: number) =>
    `/leave_request/${leaveRequestId}/approve`,
  approve_update: (leaveRequestId: number) =>
    `/leave_request/${leaveRequestId}/approve_update`,
  decline: (leaveRequestId: number) =>
    `/leave_request/${leaveRequestId}/decline`,
  patch: (leaveRequestId: number) => `/leave_request/${leaveRequestId}`,
  delete: (leaveRequestId: number) => `/leave_request/${leaveRequestId}/delete`,
  download: (file_name: string) => `/leave_request/${file_name}/download`,
  table: "leave_request/draw",
};

export const LEAVE_REQUESTS_QUERY_KEY = "leaveRequest-table";
