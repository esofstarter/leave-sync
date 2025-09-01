export interface LeaveRequestFormItem {
  id?: number;
  user_id?: number;
  leave_type_id?: number;
  start_date?: string;
  end_date?: string;
  status?: number;
  reason?: string;
  request_to?: number;
  confirmed_by?: number;
  is_confirmed?: number;
}
