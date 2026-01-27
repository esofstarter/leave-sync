import type { ModulesRoutesData } from "@/types/routes";

export const NATIONAL_HOLIDAYS_ROUTES = {
  main: "main",
  add: "add",
  edit: "edit",
} as const;

type NationalHolidaysRoutes =
  (typeof NATIONAL_HOLIDAYS_ROUTES)[keyof typeof NATIONAL_HOLIDAYS_ROUTES];

export const NATIONAL_HOLIDAY_ROUTES_DATA: ModulesRoutesData<NationalHolidaysRoutes> = {
  main: {
    path: "national_holidays",
    name: "national_holidays",
    translationKey: "admin.national_holidays.main",
  },
  add: {
    path: "national_holiday/add",
    name: "add.national_holiday",
    translationKey: "admin.national_holidays.add",
  },
  edit: {
    path: "national_holiday/:nationalHolidayId",
    name: "edit.national_holiday",
    translationKey: "admin.national_holidays.edit",
  },
};
