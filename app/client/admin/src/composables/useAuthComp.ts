import { useAuth } from "@websanova/vue-auth/src/v3.js";
import { computed } from "vue";
import { useRouter } from "vue-router";

export default function useAuthComp() {
  const auth = useAuth();
  const router = useRouter();

  const user = computed(() => auth.user());
  const permissionsArray = computed<Array<string>>(
    () => user.value.permissions_array,
  );

  function fetch(data) {
    return auth.fetch(data);
  }

  function refresh(data) {
    return auth.refresh(data);
  }

  function login(data: any): Promise<any> {
    data = data || {};

    return new Promise((resolve, reject) => {
      auth
        .login({
          data: data.data,
          remember: data.remember,
          staySignedIn: data.staySignedIn,
          fetchUser: false,
          redirect: false,
        })
        .then(async (response: any) => {
          const res = response?.response ?? response;
          const status = res?.status;

          if (status !== 200) {
            return reject({
              status: status ?? 0,
              message:
                res?.data?.error ??
                res?.data?.message ??
                "Invalid credentials",
              raw: response,
            });
          }

          try {
            await auth.fetch();
          } catch (e) {
            console.log(e);
          }

          router.push("/admin/dashboard");

          resolve(response);
        })
        .catch((error: any) => {
          const status = error?.response?.status ?? error?.status ?? 0;
          const message =
            error?.response?.data?.error ??
            error?.response?.data?.message ??
            error?.message ??
            "Invalid credentials";

          reject({ status, message, raw: error });
        });
    });
  }



  function register(data) {
    data = data || {};

    return new Promise((resolve, reject) => {
      auth
        .register({
          url: "auth/register",
          data: data.body,
          autoLogin: false,
        })
        .then((res) => {
          if (data.autoLogin) {
            login(data).then(resolve, reject);
          }
        }, reject);
    });
  }

  function impersonate(data) {
    return auth.impersonate({
      url: "auth/" + data.user.id + "/impersonate",
      redirect: {
        name: "user-account",
      },
    });
  }

  function unimpersonate() {
    return auth.unimpersonate({
      redirect: {
        name: "admin-users",
      },
    });
  }

  function logout() {
    return auth.logout({
      redirect: {
        name: "login",
      },
    });
  }

  function impersonating() {
    return auth.impersonating();
  }

  return {
    fetch,
    refresh,
    login,
    register,
    impersonate,
    unimpersonate,
    logout,
    impersonating,
    user,
    permissionsArray,
  };
}
