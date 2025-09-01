import { useQuery, useMutation, useQueryClient } from "@tanstack/vue-query";
import axios from "axios";
import { computed, ref } from "vue";
import { useRouter } from "vue-router";
import { useToast } from "vue-toastification";
import { LEAVE_REQUEST_API_ENDPOINTS } from "../constants";
import type { LeaveRequestFormItem, GetLeaveRequestResponse } from "../types";

const LEAVE_REQUEST_CACHE_KEY = "leave_request";

export const useLeaveRequestsForm = (leaveRequestId?: number) => {
  const queryClient = useQueryClient();
  const toast = useToast();
  const router = useRouter();
  const manualLoading = ref(false);

  const { isLoading: isFetching, data: queryData } = useQuery({
    queryKey: [LEAVE_REQUEST_CACHE_KEY, leaveRequestId],
    queryFn: async (): Promise<GetLeaveRequestResponse> => {
      const data = await axios.get(
        LEAVE_REQUEST_API_ENDPOINTS.get(leaveRequestId ?? 0),
      );
      return data.data;
    },
    enabled: !!leaveRequestId,
  });
  const { data: leaveRequests, isLoading } = useQuery({
    queryKey: ["leave_request/all"],
    queryFn: async (): Promise<GetLeaveRequestResponse[]> => {
      const { data } = await axios.get(LEAVE_REQUEST_API_ENDPOINTS.list);
      return data;
    },
  });

  const { mutate: createLeaveRequest, isPending: isCreating } = useMutation({
    mutationFn: async (
      newUserData: LeaveRequestFormItem,
    ): Promise<GetLeaveRequestResponse> => {
      manualLoading.value = true;
      const data = await axios.post(
        LEAVE_REQUEST_API_ENDPOINTS.create,
        newUserData,
      );
      return data.data;
    },
    onSuccess: async () => {
      toast.success("Leave Request is created!");
      router.push({ name: "leave_requests" });
      manualLoading.value = false;
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

  const { mutate: updateLeaveRequest, isPending: isUpdating } = useMutation({
    mutationFn: async (
      data: LeaveRequestFormItem,
    ): Promise<GetLeaveRequestResponse> => {
      manualLoading.value = true;
      const response = await axios.patch(
        LEAVE_REQUEST_API_ENDPOINTS.patch(leaveRequestId ?? 0),
        data,
      );
      return response.data;
    },
    onSuccess: async () => {
      queryClient.invalidateQueries({
        queryKey: [LEAVE_REQUEST_CACHE_KEY, leaveRequestId],
      });
      toast.success("Leave Request updated!");
      router.push({ name: "leave_requests" });
      manualLoading.value = false;
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

  const { mutate: approveLeaveRequest, isPending: isConfrirming } = useMutation(
    {
      mutationFn: async (
        data: LeaveRequestFormItem,
      ): Promise<GetLeaveRequestResponse> => {
        manualLoading.value = true;
        const response = await axios.post(
          LEAVE_REQUEST_API_ENDPOINTS.approve(leaveRequestId ?? 0),
          data,
        );
        return response.data;
      },
      onSuccess: async () => {
        queryClient.invalidateQueries({
          queryKey: [LEAVE_REQUEST_CACHE_KEY, leaveRequestId],
        });
        toast.success("Leave Request approved!");
        manualLoading.value = false;
      },
      onError: (error) => {
        manualLoading.value = false;
        toast.error(error.message);
      },
    },
  );

  const { mutate: approveUpdateLeaveRequest, isPending: isUpdateConfrirming } = useMutation(
    {
      mutationFn: async (
        data: LeaveRequestFormItem,
      ): Promise<GetLeaveRequestResponse> => {
        manualLoading.value = true;
        const response = await axios.post(
          LEAVE_REQUEST_API_ENDPOINTS.approve_update(leaveRequestId ?? 0),
          data,
        );
        return response.data;
      },
      onSuccess: async () => {
        queryClient.invalidateQueries({
          queryKey: [LEAVE_REQUEST_CACHE_KEY, leaveRequestId],
        });
        toast.success("Leave Request Update approved!");
        manualLoading.value = false;
      },
      onError: (error) => {
        manualLoading.value = false;
        toast.error(error.message);
      },
    },
  );

  const { mutate: declineLeaveRequest, isPending: isDeclining } = useMutation({
    mutationFn: async (
      data: LeaveRequestFormItem,
    ): Promise<GetLeaveRequestResponse> => {
      manualLoading.value = true;
      const response = await axios.post(
        LEAVE_REQUEST_API_ENDPOINTS.decline(leaveRequestId ?? 0),
        data,
      );
      return response.data;
    },
    onSuccess: async () => {
      queryClient.invalidateQueries({
        queryKey: [LEAVE_REQUEST_CACHE_KEY, leaveRequestId],
      });
      toast.success("Leave Request declined!");
      manualLoading.value = false;
    },
    onError: (error) => {
      manualLoading.value = false;
      toast.error(error.message);
    },
  });

  const { mutate: deleteLeaveRequest, isPending: isDeleting } = useMutation({
    mutationFn: async (leaveRequestId: number) => {
      await axios.post(LEAVE_REQUEST_API_ENDPOINTS.delete(leaveRequestId));
    },
    onSuccess: async () => {
      queryClient
        .invalidateQueries({ queryKey: ["leave_request/draw"] })
        .then(() => {
          toast.success("Leave Request deleted!");
        });
    },
    onError: (error) => {
      toast.error(error.message);
    },
  });

  const { mutate: downloadLeaveRequestPDF, isPending: isDownloading } =
    useMutation({
      mutationFn: async (file_name: string) => {
        await axios.get(LEAVE_REQUEST_API_ENDPOINTS.download(file_name));
      },
      onSuccess: async () => {
        toast.success("Leave Request Downloaded!");
      },
      onError: (error) => {
        toast.error(error.message);
      },
    });

  const data = computed(() => queryData.value);

  return {
    data,
    createLeaveRequest,
    updateLeaveRequest,
    approveLeaveRequest,
    approveUpdateLeaveRequest,
    declineLeaveRequest,
    deleteLeaveRequest,
    downloadLeaveRequestPDF,
    leaveRequests,
    isLoading: manualLoading,
  };
};
