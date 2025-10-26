import axios from "axios";

const api = axios.create({
    baseURL: "/", // same origin
    withCredentials: true, // send session cookie
    headers: { Accept: "application/json" },
});

// if a state-changing call hits 419 (CSRF), fetch csrf cookie and retry once
let retried = new WeakSet();
api.interceptors.response.use(
    (res) => res,
    async (error) => {
        const { response, config } = error || {};
        if (
            response &&
            response.status === 419 &&
            config &&
            !retried.has(config)
        ) {
            retried.add(config);
            await api.get("/sanctum/csrf-cookie");
            return api.request(config);
        }
        return Promise.reject(error);
    }
);

export default api;
