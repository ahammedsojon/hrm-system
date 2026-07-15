import api from './api';

export const departmentService = {
    list(params = {}) {
        return api.get('/departments', { params });
    },

    get(id) {
        return api.get(`/departments/${id}`);
    },

    create(data) {
        return api.post('/departments', data);
    },

    update(id, data) {
        return api.put(`/departments/${id}`, data);
    },

    delete(id) {
        return api.delete(`/departments/${id}`);
    },
};
