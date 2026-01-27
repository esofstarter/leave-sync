import { DatatableColumns } from "@starter-core/dash-ui/src";

export const NATIONAL_HOLIDAYS_DATATABLE_COLUMNS: DatatableColumns = [
  {
    id: 0,
    label: "strings.date",
    name: "date",
    sortable: true,
  },
  {
    id: 1,
    label: "strings.country",
    name: "country",
    sortable: true,
  },
  {
    id: 2,
    label: "strings.year",
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
