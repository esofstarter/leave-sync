import type { ModulesRoutesData } from "@/types/routes";

export const USERS_ROUTES = {
  main: "main",
  add: "add",
  edit: "edit",
  myProfile: "myProfile",
} as const;

type UsersRoutes = (typeof USERS_ROUTES)[keyof typeof USERS_ROUTES];

export const USER_ROUTES_DATA: ModulesRoutesData<UsersRoutes> = {
  main: {
    path: "users",
    name: "users",
    translationKey: "admin.users.main",
  },
  add: {
    path: "user/add",
    name: "add.user",
    translationKey: "admin.users.add",
  },
  edit: {
    path: "user/:userId",
    name: "edit.user",
    translationKey: "admin.users.edit",
  },
  myProfile: {
    path: "myprofile",
    name: "myprofile",
    translationKey: "admin.myprofile",
  },
};
