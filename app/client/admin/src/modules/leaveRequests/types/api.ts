import type { Permission, UserRoleId } from "./permissions";
import { LeaveTypeFormItem } from "@/modules/leaveTypes/types";
import { UserFormItem } from "@/modules/users/types";
import type { PaginationObject } from "@starter-core/dash-ui/src/components";

export interface GetLeaveRequestResponse {
  id: number;
  user_id: number;
  leave_type_id: number;
  start_date: Date;
  end_date: Date;
  days: number;
  status: number;
  reason: string;
  request_to: number;
  confirmed_by: number;
  is_confirmed: number;
  user: UserFormItem;
  leaveType: LeaveTypeFormItem;
  requestToUser: UserFormItem;
}

export interface LeaveRequestsTableResponse {
  data: GetLeaveRequestResponse[];
  pagination: PaginationObject;
}
