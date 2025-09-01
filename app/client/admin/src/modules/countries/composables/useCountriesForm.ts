import { useQuery, useMutation, useQueryClient } from "@tanstack/vue-query";
import axios from "axios";
import { computed, ref } from "vue";
import { useRouter } from "vue-router";
import { useToast } from "vue-toastification";
import { COUNTRY_API_ENDPOINTS } from "../constants";
import type { CountryFormItem, GetCountryResponse } from "../types";

const COUNTRY_CACHE_KEY = "country";

export const useCountriesForm = (countryId?: number) => {
  const queryClient = useQueryClient();
  const toast = useToast();
  const router = useRouter();
  const manualLoading = ref(false);

  const { isLoading: isFetching, data: queryData } = useQuery({
    queryKey: [COUNTRY_CACHE_KEY, countryId],
    queryFn: async (): Promise<GetCountryResponse> => {
      const data = await axios.get(COUNTRY_API_ENDPOINTS.get(countryId ?? 0));
      return data.data;
    },
    enabled: !!countryId,
  });

  const { mutate: createCountry, isPending: isCreating } = useMutation({
    mutationFn: async (
      newUserData: CountryFormItem,
    ): Promise<GetCountryResponse> => {
      manualLoading.value = true;
      const data = await axios.post(COUNTRY_API_ENDPOINTS.create, newUserData);
      return data.data;
    },
    onSuccess: async () => {
      router.push({ name: "countries" });
      manualLoading.value = false;
      toast.success("Country created!");
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

  const { mutate: updateCountry, isPending: isUpdating } = useMutation({
    mutationFn: async (data: CountryFormItem): Promise<GetCountryResponse> => {
      manualLoading.value = true;
      const response = await axios.patch(
        COUNTRY_API_ENDPOINTS.patch(countryId ?? 0),
        data,
      );
      return response.data;
    },
    onSuccess: async () => {
      queryClient.invalidateQueries({
        queryKey: [COUNTRY_CACHE_KEY, countryId],
      });
      router.push({ name: "countries" });
      manualLoading.value = false;
      toast.success("Country updated!");
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

  const { mutate: deleteCountry, isPending: isDeleting } = useMutation({
    mutationFn: async (countryId: number) => {
      await axios.post(COUNTRY_API_ENDPOINTS.delete(countryId));
    },
    onSuccess: async () => {
      queryClient.invalidateQueries({ queryKey: ["country/draw"] }).then(() => {
        toast.success("Country deleted!");
      });
    },
    onError: (error) => {
      toast.error(error.message);
    },
  });

  const data = computed(() => queryData.value);

  return {
    data,
    createCountry,
    updateCountry,
    deleteCountry,
    isLoading: manualLoading,
  };
};
