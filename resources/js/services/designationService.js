import api from './api';

export const designationService = {
    list(params = {}) {
        return api.get('/designations', { params });
    },

    get(id) {
        return api.get(`/designations/${id}`);
    },

    create(data) {
        return api.post('/designations', data);
    },

    update(id, data) {
        return api.put(`/designations/${id}`, data);
    },

    delete(id) {
        return api.delete(`/designations/${id}`);
    },
};
