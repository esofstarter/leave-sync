import { DatatableColumns } from "@starter-core/dash-ui/src";

export const LEAVE_REQUESTS_DATATABLE_COLUMNS: DatatableColumns = [
  {
    id: 0,
    label: "Leave Type",
    name: "leave_type_id",
    sortable: true,
  },
  {
    id: 1,
    label: "From",
    name: "request_to",
    sortable: true,
  },
  {
    id: 2,
    label: "Assigned To",
    name: "request_to",
    sortable: true,
  },
  {
    id: 3,
    label: "Status",
    name: "status",
    sortable: true,
  },
  {
    id: 4,
    label: "From (Date)",
    name: "start_date",
    sortable: true,
  },
  {
    id: 5,
    label: "To (Date)",
    name: "end_date",
    sortable: true,
  },
  {
    id: 6,
    label: "Days",
    name: "days",
    sortable: true,
  },
  {
    id: 7,
    label: "PDF",
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
