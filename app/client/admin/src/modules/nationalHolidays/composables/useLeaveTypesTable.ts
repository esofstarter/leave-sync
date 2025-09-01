import { useQuery, UseQueryReturnType } from "@tanstack/vue-query";
import axios, { type AxiosError } from "axios";
import type { ComputedRef } from "vue";
import { NATIONAL_HOLIDAY_API_ENDPOINTS, NATIONAL_HOLIDAYS_QUERY_KEY } from "../constants";
import type { UsersTableResponse } from "../types";
import type { TableQuery } from "@starter-core/dash-ui/src";

export const useLeaveTypesTable = (
  query: ComputedRef<TableQuery>,
): UseQueryReturnType<UsersTableResponse, AxiosError> => {
  return useQuery({
    queryKey: [NATIONAL_HOLIDAYS_QUERY_KEY, query],
    queryFn: async () => {
      const response = await axios.get(NATIONAL_HOLIDAY_API_ENDPOINTS.table, {
        params: query.value,
      });
      return response.data;
    },
  });
};
