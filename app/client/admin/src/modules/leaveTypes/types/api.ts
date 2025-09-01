import type { Permission, UserRoleId } from "./permissions";
import type { PaginationObject } from "@starter-core/dash-ui/src/components";

export interface GetLeaveTypeResponse {
  id: number;
  slug: string;
  name: string;
  is_paid: boolean;
  color: string;
}

export interface UsersTableResponse {
  data: GetLeaveTypeResponse[];
  pagination: PaginationObject;
}
