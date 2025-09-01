import type { PaginationObject } from "@starter-core/dash-ui/src/components";

export interface GetNationalHolidayResponse {
  id: number;
  country: string;
  year: number;
  date: Date;
}

export interface UsersTableResponse {
  data: GetNationalHolidayResponse[];
  pagination: PaginationObject;
}
