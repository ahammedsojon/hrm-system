import api from './api';

export const emergencyContactService = {
    list(params = {}) {
        return api.get('/emergency-contacts', { params });
    },

    get(id) {
        return api.get(`/emergency-contacts/${id}`);
    },

    create(data) {
        return api.post('/emergency-contacts', data);
    },

    update(id, data) {
        return api.put(`/emergency-contacts/${id}`, data);
    },

    delete(id) {
        return api.delete(`/emergency-contacts/${id}`);
    },
};
