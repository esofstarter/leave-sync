<script setup lang="ts">
  import { ref } from "vue";
  import { useRouter } from "vue-router";
  import useAuthComp from "@/composables/useAuthComp";

  const form = ref({ email: "", password: "" });
  const formErrors = ref({ email: "", password: "" });
  const authError = ref(false);
  const staySignedIn = ref(true);
  const isLoading = ref(false);

  const router = useRouter();
  const { login } = useAuthComp();

  const validateForm = () => {
    let isValid = true;
    formErrors.value.email = "";
    formErrors.value.password = "";

    if (!form.value.email) {
      formErrors.value.email = "Email is required";
      isValid = false;
    }
    if (!form.value.password) {
      formErrors.value.password = "Password is required";
      isValid = false;
    }

    return isValid;
  };

  const submitForm = async () => {
    if (!validateForm()) return;

    isLoading.value = true;

    try {
      await login({
        data: form.value,
        redirect: false,
        remember: false,
        staySignedIn: staySignedIn.value,
      });
    } catch (error) {
      authError.value = true;
      console.log(error);
    } finally {
      isLoading.value = false;
    }
  };
</script>

<template>
  <div class="auth-login">
    <div class="auth-base__head">
      <h3 class="auth-base__title">LeaveSync</h3>
    </div>
    <form class="kt-form auth-base__form" @submit.prevent="submitForm">
      <div class="input-group">
        <input
          v-model="form.email"
          class="form-control"
          type="text"
          placeholder="admin@example.com"
          name="email"
          autocomplete="off"
          required
          autofocus
        />
      </div>
      <div class="input-group">
        <input
          v-model="form.password"
          class="form-control"
          type="password"
          placeholder="password"
          name="password"
          required
        />
      </div>
      <span v-if="authError" class="error invalid-feedback">
        Authentication failed
      </span>
      <div class="row auth-base__extra">
        <div class="col">
          <label class="kt-checkbox">
            <input v-model="staySignedIn" type="checkbox" name="remember" />
            Remember Me
            <span></span>
          </label>
        </div>
      </div>
      <div class="auth-base__actions">
        <button
          class="btn btn-brand btn-elevate auth-base__btn-primary"
          type="submit"
          :disabled="isLoading"
        >
          <span v-if="isLoading" class="spinner"></span> Sign In
        </button>
      </div>
    </form>
  </div>
</template>

<style scoped>
  /* 🔄 Loading Spinner */
  .spinner {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid black;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 0.8s linear infinite;
    margin-right: 8px;
  }

  @keyframes spin {
    to {
      transform: rotate(360deg);
    }
  }
</style>
