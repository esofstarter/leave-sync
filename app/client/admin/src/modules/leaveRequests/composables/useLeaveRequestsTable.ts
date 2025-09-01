import { useQuery } from "@tanstack/vue-query";
import axios, { type AxiosError } from "axios";
import type { ComputedRef } from "vue";
import {
  LEAVE_REQUEST_API_ENDPOINTS,
  LEAVE_REQUESTS_QUERY_KEY,
} from "../constants";
import type { LeaveRequestsTableResponse } from "../types";
import type { TableQuery } from "@starter-core/dash-ui/src";

export const useLeaveRequestsTable = (query: ComputedRef<TableQuery>) => {
  const queryResult = useQuery<LeaveRequestsTableResponse, AxiosError>({
    queryKey: [LEAVE_REQUESTS_QUERY_KEY, query.value],
    queryFn: async () => {
      const response = await axios.get(LEAVE_REQUEST_API_ENDPOINTS.table, {
        params: query.value,
      });
      return response.data;
    },
  });

  return queryResult;
};
