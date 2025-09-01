export const USER_PERMISSIONS = {
  readRequests: "read_requests",
  writeRequests: "write_requests",
  deleteRequests: "delete_requests",
  approveRequests: "approve_requests",
} as const;

export const USER_ROLES = {
  admin: 1,
  editor: 2,
  collaborator: 3,
} as const;
