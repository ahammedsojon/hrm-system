import api from './api';

export const employeeService = {
    list(params = {}) {
        return api.get('/employees', { params });
    },

    get(id) {
        return api.get(`/employees/${id}`);
    },

    create(data) {
        return api.post('/employees', data);
    },

    update(id, data) {
        return api.post(`/employees/${id}`, data);
    },

    delete(id) {
        return api.delete(`/employees/${id}`);
    },

    managers(exclude = null) {
        return api.get('/employees/managers', {
            params: exclude ? { exclude } : {},
        });
    },
};
