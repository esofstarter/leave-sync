import { useQuery, UseQueryReturnType } from "@tanstack/vue-query";
import axios, { type AxiosError } from "axios";
import type { ComputedRef } from "vue";
import { LEAVE_TYPE_API_ENDPOINTS, LEAVE_TYPES_QUERY_KEY } from "../constants";
import type { UsersTableResponse } from "../types";
import type { TableQuery } from "@starter-core/dash-ui/src";

export const useLeaveTypesTable = (
  query: ComputedRef<TableQuery>,
): UseQueryReturnType<UsersTableResponse, AxiosError> => {
  return useQuery({
    queryKey: [LEAVE_TYPES_QUERY_KEY, query],
    queryFn: async () => {
      const response = await axios.get(LEAVE_TYPE_API_ENDPOINTS.table, {
        params: query.value,
      });
      return response.data;
    },
  });
};
