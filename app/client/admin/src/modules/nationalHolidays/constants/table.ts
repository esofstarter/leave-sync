import { DatatableColumns } from "@starter-core/dash-ui/src";

export const NATIONAL_HOLIDAYS_DATATABLE_COLUMNS: DatatableColumns = [
  {
    id: 0,
    label: "Date (yyyy-mm-dd)",
    name: "date",
    sortable: true,
  },
  {
    id: 1,
    label: "Country",
    name: "country",
    sortable: true,
  },
  {
    id: 2,
    label: "Year",
    name: "year",
    sortable: true,
  },
  {
    id: 3,
    label: "strings.actions",
    name: "actions",
  },
  {
    id: 4,
    label: "strings.delete",
    name: "delete",
  },
];
