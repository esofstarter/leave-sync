import { DatatableColumns } from "@starter-core/dash-ui/src";

export const LEAVE_TYPES_DATATABLE_COLUMNS: DatatableColumns = [
  {
    id: 0,
    label: "strings.slug",
    name: "slug",
    sortable: false,
  },
  {
    id: 1,
    label: "strings.name",
    name: "name",
    sortable: true,
  },
  {
    id: 2,
    label: "strings.color",
    name: "color",
    sortable: false,
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
