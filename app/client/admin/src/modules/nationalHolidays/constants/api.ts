export const NATIONAL_HOLIDAY_API_ENDPOINTS = {
  get: (nationalHolidayId: number) => `/national_holiday/${nationalHolidayId}`,
  create: "/national_holiday/create",
  patch: (nationalHolidayId: number) => `/national_holiday/${nationalHolidayId}`,
  delete: (nationalHolidayId: number) => `/national_holiday/${nationalHolidayId}/delete`,
  table: "national_holiday/draw",
};

export const NATIONAL_HOLIDAYS_QUERY_KEY = "nationalHoliday-table";
