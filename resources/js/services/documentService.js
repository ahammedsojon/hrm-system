import api from './api';

export const documentService = {
    list(params = {}) {
        return api.get('/documents', { params });
    },

    get(id) {
        return api.get(`/documents/${id}`);
    },

    create(data) {
        return api.post('/documents', data);
    },

    update(id, data) {
        return api.post(`/documents/${id}`, data);
    },

    delete(id) {
        return api.delete(`/documents/${id}`);
    },
};
