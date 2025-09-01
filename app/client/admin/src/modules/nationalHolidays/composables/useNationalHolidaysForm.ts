import { useQuery, useMutation, useQueryClient } from "@tanstack/vue-query";
import axios from "axios";
import { computed, ref } from "vue";
import { useRouter } from "vue-router";
import { useToast } from "vue-toastification";
import { NATIONAL_HOLIDAY_API_ENDPOINTS } from "../constants";
import type { NationalHolidayFormItem, GetNationalHolidayResponse } from "../types";

const NATIONAL_HOLIDAY_CACHE_KEY = "national_holiday";

export const useNationalHolidaysForm = (nationalHolidayId?: number) => {
  const queryClient = useQueryClient();
  const toast = useToast();
  const router = useRouter();
  const manualLoading = ref(false);

  const { isLoading: isFetching, data: queryData } = useQuery({
    queryKey: [NATIONAL_HOLIDAY_CACHE_KEY, nationalHolidayId],
    queryFn: async (): Promise<GetNationalHolidayResponse> => {
      const data = await axios.get(
        NATIONAL_HOLIDAY_API_ENDPOINTS.get(nationalHolidayId ?? 0),
      );
      return data.data;
    },
    enabled: !!nationalHolidayId,
  });

  const { mutate: createNationalHoliday, isPending: isCreating } = useMutation({
    mutationFn: async (
      newUserData: NationalHolidayFormItem,
    ): Promise<GetNationalHolidayResponse> => {
      manualLoading.value = true;
      const data = await axios.post(
        NATIONAL_HOLIDAY_API_ENDPOINTS.create,
        newUserData,
      );
      return data.data;
    },
    onSuccess: async () => {
      router.push({ name: "national_holidays" });
      manualLoading.value = false;
      toast.success("Holiday created!");
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

  const { mutate: updateNationalHoliday, isPending: isUpdating } = useMutation({
    mutationFn: async (
      data: NationalHolidayFormItem,
    ): Promise<GetNationalHolidayResponse> => {
      manualLoading.value = true;
      const response = await axios.patch(
        NATIONAL_HOLIDAY_API_ENDPOINTS.patch(nationalHolidayId ?? 0),
        data,
      );
      return response.data;
    },
    onSuccess: async () => {
      queryClient.invalidateQueries({
        queryKey: [NATIONAL_HOLIDAY_CACHE_KEY, nationalHolidayId],
      });
      router.push({ name: "national_holidays" });
      manualLoading.value = false;
      toast.success("Holiday updated!");
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

  const { mutate: deleteNationalHoliday, isPending: isDeleting } = useMutation({
    mutationFn: async (nationalHolidayId: number) => {
      await axios.post(NATIONAL_HOLIDAY_API_ENDPOINTS.delete(nationalHolidayId));
    },
    onSuccess: async () => {
      queryClient
        .invalidateQueries({ queryKey: ["national_holiday/draw"] })
        .then(() => {
          toast.success("Holiday deleted!");
        });
    },
    onError: (error) => {
      toast.error(error.message);
    },
  });

  const data = computed(() => queryData.value);

  return {
    data,
    createNationalHoliday,
    updateNationalHoliday,
    deleteNationalHoliday,
    isLoading: manualLoading,
  };
};
