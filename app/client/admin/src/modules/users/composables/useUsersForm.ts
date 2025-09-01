import { useQuery, useMutation, useQueryClient } from "@tanstack/vue-query";
import axios from "axios";
import { computed, ref } from "vue";
import { useRouter } from "vue-router";
import { useToast } from "vue-toastification";
import { USER_API_ENDPOINTS } from "../constants";
import type { UserFormItem, GetUserResponse } from "../types";

const USER_CACHE_KEY = "user";

export const useUsersForm = (userId?: number) => {
  const queryClient = useQueryClient();
  const toast = useToast();
  const router = useRouter();
  const manualLoading = ref(false);

  const { isLoading: isFetching, data: queryData } = useQuery({
    queryKey: [USER_CACHE_KEY, userId],
    queryFn: async (): Promise<GetUserResponse> => {
      const data = await axios.get(USER_API_ENDPOINTS.get(userId ?? 0));
      return data.data;
    },
    enabled: !!userId,
  });

  const { data: users } = useQuery({
    queryKey: ["user/draw"],
    queryFn: async () => {
      const response = await axios.get(USER_API_ENDPOINTS.table, {
        params: data.value,
      });
      return response.data;
    },
  });

  const { mutate: createUser, isPending: isCreating } = useMutation({
    mutationFn: async (newUserData: UserFormItem): Promise<GetUserResponse> => {
      manualLoading.value = true;
      const data = await axios.post(USER_API_ENDPOINTS.create, newUserData);
      return data.data;
    },
    onSuccess: async () => {
      toast.success("User saved!");
      router.push({ name: "users" });
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

  const { mutate: updateUser, isPending: isUpdating } = useMutation({
    mutationFn: async (data: UserFormItem): Promise<GetUserResponse> => {
      manualLoading.value = true;
      const response = await axios.patch(
        USER_API_ENDPOINTS.patch(userId ?? 0),
        data,
      );
      return response.data;
    },
    onSuccess: async () => {
      queryClient.invalidateQueries({ queryKey: [USER_CACHE_KEY, userId] });
      toast.success("User updated!");
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

  const { mutate: uploadAvatar, isPending: isUploadingAvatar } = useMutation({
    mutationFn: async (file: File): Promise<GetUserResponse> => {
      manualLoading.value = true;
      const formData = new FormData();
      formData.append("avatar", file);

      const response = await axios.post(
        USER_API_ENDPOINTS.uploadAvatar(userId ?? 0),
        formData,
        {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        },
      );
      return response.data;
    },
    onSuccess: async () => {
      queryClient.invalidateQueries({ queryKey: [USER_CACHE_KEY, userId] });
      toast.success("Image Updated!");
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

  const { mutate: deleteUser, isPending: isDeleting } = useMutation({
    mutationFn: async (userId: number) => {
      await axios.post(USER_API_ENDPOINTS.delete(userId));
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["user/draw"] }).then(() => {
        toast.success("User deleted!");
      });
    },
    onError: () => {
      toast.error("Error deleting user!");
    },
  });

  const data = computed(() => queryData.value);

  return {
    data,
    createUser,
    updateUser,
    uploadAvatar,
    deleteUser,
    users,
    isLoading: manualLoading,
  };
};
