export const COUNTRY_API_ENDPOINTS = {
  get: (countryId: number) => `/country/${countryId}`,
  create: "/country/create",
  patch: (countryId: number) => `/country/${countryId}`,
  delete: (countryId: number) => `/country/${countryId}/delete`,
  table: "country/draw",
};

export const COUNTRIES_QUERY_KEY = "country-table";
