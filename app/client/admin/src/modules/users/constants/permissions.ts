export const USER_PERMISSIONS = {
  readUsers: "read_users",
  writeUsers: "write_users",
  deleteUsers: "delete_users",
  writeProfile: "write_profile",
} as const;

export const USER_ROLES = {
  admin: 1,
  editor: 2,
  collaborator: 3,
} as const;
