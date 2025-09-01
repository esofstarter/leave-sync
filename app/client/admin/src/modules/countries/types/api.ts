import type { Permission, UserRoleId } from "./permissions";
import type { PaginationObject } from "@starter-core/dash-ui/src/components";

export interface GetCountryResponse {
  id: number;
  name: string;
  country_code: string;
}

export interface UsersTableResponse {
  data: GetCountryResponse[];
  pagination: PaginationObject;
}
