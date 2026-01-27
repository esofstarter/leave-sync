import { DatatableColumns } from "@starter-core/dash-ui/src";

export const COUNTRIES_DATATABLE_COLUMNS: DatatableColumns = [
  {
    id: 0,
    label: "strings.country_code",
    name: "country_code",
    sortable: true,
  },
  {
    id: 1,
    label: "strings.name",
    name: "name",
    sortable: true,
  },
  {
    id: 2,
    label: "strings.actions",
    name: "actions",
  },
  {
    id: 3,
    label: "strings.delete",
    name: "delete",
  },
];
