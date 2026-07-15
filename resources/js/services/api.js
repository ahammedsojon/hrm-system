import axios from 'axios';

const api = axios.create({
    baseURL: '/api',
    withCredentials: true,
    headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
    xsrfCookieName: 'XSRF-TOKEN',
    xsrfHeaderName: 'X-XSRF-TOKEN',
});

api.interceptors.response.use(
    (response) => response,
    async (error) => {
        const status = error.response?.status;

        if (status === 401) {
            const requestUrl = error.config?.url ?? '';
            const isSessionCheck = requestUrl === '/user' || requestUrl.endsWith('/user');

            if (!isSessionCheck) {
                const { default: router } = await import('../router');

                if (router.currentRoute.value.name !== 'login') {
                    router.push({ name: 'login' });
                }
            }
        }

        if (status === 419) {
            window.location.reload();
        }

        return Promise.reject(error);
    },
);

export default api;
