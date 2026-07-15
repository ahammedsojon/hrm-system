import api from './api';

export const employmentTypeService = {
    list(params = {}) {
        return api.get('/employment-types', { params });
    },

    get(id) {
        return api.get(`/employment-types/${id}`);
    },

    create(data) {
        return api.post('/employment-types', data);
    },

    update(id, data) {
        return api.put(`/employment-types/${id}`, data);
    },

    delete(id) {
        return api.delete(`/employment-types/${id}`);
    },
};
