import api from './api';

export const shiftService = {
    list(params = {}) {
        return api.get('/shifts', { params });
    },

    get(id) {
        return api.get(`/shifts/${id}`);
    },

    create(data) {
        return api.post('/shifts', data);
    },

    update(id, data) {
        return api.put(`/shifts/${id}`, data);
    },

    delete(id) {
        return api.delete(`/shifts/${id}`);
    },
};
