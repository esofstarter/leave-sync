import type { NationalHolidayFormItem } from "../types";

const nationalHoliday: NationalHolidayFormItem = {
  id: 0,
  date: new Date(),
  country: "",
  year: new Date().getFullYear(),
};

export { nationalHoliday };
