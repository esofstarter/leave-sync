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
    authError.value = false;

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

      const wrapper = document.querySelector(".auth-login");
      wrapper?.classList.add("shake");

      setTimeout(() => wrapper?.classList.remove("shake"), 300);

      console.log(error);
    } finally {
      isLoading.value = false;
    }
  };
</script>

<template>
  <div class="auth-login">
    <div class="auth-base__head">
      <h3 class="auth-base__title">ESOF LeaveSync</h3>
    </div>
    <form class="kt-form auth-base__form" @submit.prevent="submitForm">
      <div class="input-group">
      <input
          v-model="form.email"
          class="form-control"
          :class="{ 'is-invalid': formErrors.email || authError }"
          type="text"
          placeholder="admin@example.com"
          name="email"
          autocomplete="off"
          required
          autofocus
        />
        <span v-if="formErrors.email" class="error-text">
          {{ formErrors.email }}
        </span>
      </div>

      <div class="input-group">
        <input
          v-model="form.password"
          class="form-control"
          :class="{ 'is-invalid': formErrors.password || authError }"
          type="password"
          placeholder="password"
          name="password"
          required
        />
        <span v-if="formErrors.password" class="error-text">
          {{ formErrors.password }}
        </span>
      </div>

      <span v-if="authError" class="error invalid-feedback">
        Invalid email or password
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
          class="btn btn-brand btn-elevate auth-base__btn-primary login-btn"
          type="submit"
          :disabled="isLoading"
        >
          <span v-if="isLoading" class="spinner"></span>
          <span class="login-btn__text">Sign In</span>
        </button>
      </div>
    </form>
  </div>
</template>

<style scoped>
  /* 🔄 Loading Spinner */
  .spinner {
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.4);
    border-top-color: white;
  }
  @keyframes spin {
    to {
      transform: rotate(360deg);
    }
  }
  .login-btn {
  width: 100%;
  padding: 14px 20px;
  font-size: 16px;
  font-weight: 700;
  letter-spacing: 0.3px;
  border-radius: 10px;
  background: linear-gradient(135deg, #5b5ff5, #4a47ff);
  color: white;
  border: none;
  box-shadow: 0 8px 20px rgba(74, 71, 255, 0.35);
  transition: all 0.18s ease-in-out;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

/* Hover = stronger pop */
.login-btn:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 12px 26px rgba(74, 71, 255, 0.45);
  background: linear-gradient(135deg, #6368ff, #5450ff);
}

/* Active press effect */
.login-btn:active:not(:disabled) {
  transform: translateY(1px);
  box-shadow: 0 5px 14px rgba(74, 71, 255, 0.35);
}

/* Disabled state */
.login-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Text alignment fix */
.login-btn__text {
  line-height: 1;
}
/* Error Input State */
.form-control.is-invalid {
  border-color: #dc3545;
  background: rgba(220, 53, 69, 0.03);
  box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.08);
}

/* Error Message Text */
.error-text {
  display: block;
  margin-top: 6px;
  font-size: 13px;
  color: #dc3545;
  font-weight: 500;
}

/* Auth Error Banner */
.invalid-feedback {
  display: block;
  margin-top: 10px;
  color: #dc3545;
  font-size: 14px;
  font-weight: 600;
  text-align: center;
}

/* Shake Animation for Login Failure */
.auth-login.shake {
  animation: shake 0.3s ease;
}

@keyframes shake {
  0% { transform: translateX(0); }
  25% { transform: translateX(-4px); }
  50% { transform: translateX(4px); }
  75% { transform: translateX(-4px); }
  100% { transform: translateX(0); }
}

</style>
