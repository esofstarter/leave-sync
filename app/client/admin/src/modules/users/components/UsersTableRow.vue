<script setup lang="ts">
  import { IconTrash, IconEdit } from "@starter-core/icons";
  import { useAuth } from "@websanova/vue-auth/src/v3.js";
  import { ref, computed } from "vue";
  import type { GetUserResponse } from "../types";
  import UserRoleBadge from "./UserRoleBadge.vue";
  import UserStatusBadge from "./UserStatusBadge.vue";
  import ConfirmDialog from "@/components/ConfirmDialog/ConfirmDialog.vue";

  import {
    DashButton,
    DashLink,
    TableColumn,
    TableRow,
  } from "@starter-core/dash-ui/src";

  interface UsersTableRowProps {
    user: GetUserResponse;
    isEvenRow: boolean;
    deleteUser: (id: number) => Promise<void>;
  }

  const { user, isEvenRow, deleteUser } = defineProps<UsersTableRowProps>();
  const auth = useAuth();

  const avatarSource = computed(() => {
    if (user.avatar_thumbnail) {
      return user.avatar_thumbnail;
    }
    return new URL(
      `@/../assets/images/placeholders/avatar-placeholder.jpg`,
      import.meta.url,
    ).href;
  });
  const showConfirmDialog = ref(false);

  const confirmDelete = () => {
    deleteUser(user.id);
    showConfirmDialog.value = false;
  };
</script>

<template>
  <template v-if="true">
    <TableRow :section="'body'" :is-even="isEvenRow">
      <!--kt-datatable__row&#45;&#45;even-->

      <TableColumn>
        <img :style="{ width: '50px', height: '50px' }" :src="avatarSource" />
      </TableColumn>

      <TableColumn>
        {{ user.first_name }}
      </TableColumn>

      <TableColumn>
        {{ user.last_name }}
      </TableColumn>

      <TableColumn>
        {{ user.paid_leaves_left }}
      </TableColumn>

      <TableColumn>
        {{ user.is_office_based ? "Yes" : "No" }}
      </TableColumn>

      <TableColumn>
        {{ user.email }}
      </TableColumn>

      <TableColumn>
        <UserRoleBadge :user-role-id="user.role" />
      </TableColumn>

      <TableColumn>
        <UserStatusBadge :is-disabled="user.is_disabled" />
      </TableColumn>

      <TableColumn>
        <dash-link
          v-if="auth.user().permissions_array.includes('write_users')"
          :to="{ name: 'edit.user', params: { userId: user.id } }"
          theme="primary"
          theme-mod="outline-hover"
          :icon="IconEdit"
        >
          {{ $t("buttons.edit") }}
        </dash-link>
      </TableColumn>

      <TableColumn>
        <DashButton
          v-if="auth.user().permissions_array.includes('delete_users')"
          :icon="IconTrash"
          theme="danger"
          size="sm"
          @click="showConfirmDialog = true"
          is-pill
          is-icon
        />
      </TableColumn>
    </TableRow>
    <ConfirmDialog
      :show="showConfirmDialog"
      message="Are you sure you want to delete this user?"
      @confirm="confirmDelete"
      @close="showConfirmDialog = false"
    />
  </template>
</template>
