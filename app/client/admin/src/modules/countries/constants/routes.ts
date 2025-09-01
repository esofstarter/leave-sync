import type { ModulesRoutesData } from "@/types/routes";

export const COUNTRIES_ROUTES = {
  main: "main",
  add: "add",
  edit: "edit",
} as const;

type countriesRoutes = (typeof COUNTRIES_ROUTES)[keyof typeof COUNTRIES_ROUTES];

export const COUNTRY_ROUTES_DATA: ModulesRoutesData<countriesRoutes> = {
  main: {
    path: "countries",
    name: "countries",
    translationKey: "countries",
  },
  add: {
    path: "country/add",
    name: "add.country",
    translationKey: "countries.add",
  },
  edit: {
    path: "country/:countryId",
    name: "edit.country",
    translationKey: "countries.edit_countries",
  },
};
