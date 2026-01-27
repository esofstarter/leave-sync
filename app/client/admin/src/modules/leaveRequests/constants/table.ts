import { DatatableColumns } from "@starter-core/dash-ui/src";

export const LEAVE_REQUESTS_DATATABLE_COLUMNS: DatatableColumns = [
  {
    id: 0,
    label: "strings.leave_type",
    name: "leave_type_id",
    sortable: true,
  },
  {
    id: 1,
    label: "strings.from",
    name: "request_to",
    sortable: true,
  },
  {
    id: 2,
    label: "strings.assigned_to",
    name: "request_to",
    sortable: true,
  },
  {
    id: 3,
    label: "strings.status",
    name: "status",
    sortable: true,
  },
  {
    id: 4,
    label: "strings.from_date",
    name: "start_date",
    sortable: true,
  },
  {
    id: 5,
    label: "strings.to_date",
    name: "end_date",
    sortable: true,
  },
  {
    id: 6,
    label: "strings.days",
    name: "days",
    sortable: true,
  },
  {
    id: 7,
    label: "strings.pdf",
    name: "pdf",
  },
  {
    id: 8,
    label: "strings.actions",
    name: "actions",
  },
  {
    id: 9,
    label: "strings.delete",
    name: "delete",
  },
];
