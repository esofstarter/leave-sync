import { useQuery, useMutation, useQueryClient } from "@tanstack/vue-query";
import axios from "axios";
import { computed, ref } from "vue";
import { useRouter } from "vue-router";
import { useToast } from "vue-toastification";
import { LEAVE_TYPE_API_ENDPOINTS } from "../constants";
import type { LeaveTypeFormItem, GetLeaveTypeResponse } from "../types";

const LEAVE_TYPE_CACHE_KEY = "leave_type";

export const useLeaveTypesForm = (leaveTypeId?: number) => {
  const queryClient = useQueryClient();
  const toast = useToast();
  const router = useRouter();
  const manualLoading = ref(false);

  const { isLoading: isFetching, data: queryData } = useQuery({
    queryKey: [LEAVE_TYPE_CACHE_KEY, leaveTypeId],
    queryFn: async (): Promise<GetLeaveTypeResponse> => {
      const data = await axios.get(
        LEAVE_TYPE_API_ENDPOINTS.get(leaveTypeId ?? 0),
      );
      return data.data;
    },
    enabled: !!leaveTypeId,
  });

  const { mutate: createLeaveType, isPending: isCreating } = useMutation({
    mutationFn: async (
      newUserData: LeaveTypeFormItem,
    ): Promise<GetLeaveTypeResponse> => {
      manualLoading.value = true;
      const data = await axios.post(
        LEAVE_TYPE_API_ENDPOINTS.create,
        newUserData,
      );
      return data.data;
    },
    onSuccess: async () => {
      router.push({ name: "leave_types" });
      manualLoading.value = false;
      toast.success("Leave Type created!");
    },
    onError: (error) => {
      // @ts-ignore
      const firstErrorMessage = error.errors
        ? Object.values(error.errors)[0][0]
        : "An unexpected error occurred";
      toast.error(firstErrorMessage);
      manualLoading.value = false;
    },
  });

  const { mutate: updateLeaveType, isPending: isUpdating } = useMutation({
    mutationFn: async (
      data: LeaveTypeFormItem,
    ): Promise<GetLeaveTypeResponse> => {
      manualLoading.value = true;
      const response = await axios.patch(
        LEAVE_TYPE_API_ENDPOINTS.patch(leaveTypeId ?? 0),
        data,
      );
      return response.data;
    },
    onSuccess: async () => {
      queryClient.invalidateQueries({
        queryKey: [LEAVE_TYPE_CACHE_KEY, leaveTypeId],
      });
      router.push({ name: "leave_types" });
      manualLoading.value = false;
      toast.success("Leave Type updated!");
    },
    onError: (error) => {
      // @ts-ignore
      const firstErrorMessage = error.errors
        ? Object.values(error.errors)[0][0]
        : "An unexpected error occurred";
      manualLoading.value = false;
      toast.error(firstErrorMessage);
    },
  });

  const { mutate: deleteLeaveType, isPending: isDeleting } = useMutation({
    mutationFn: async (leaveTypeId: number) => {
      await axios.post(LEAVE_TYPE_API_ENDPOINTS.delete(leaveTypeId));
    },
    onSuccess: async () => {
      queryClient
        .invalidateQueries({ queryKey: ["leave_type/draw"] })
        .then(() => {
          toast.success("Leave Type deleted!");
        });
    },
    onError: (error) => {
      toast.error(error.message);
    },
  });

  const data = computed(() => queryData.value);

  return {
    data,
    createLeaveType,
    updateLeaveType,
    deleteLeaveType,
    isLoading: manualLoading,
  };
};
