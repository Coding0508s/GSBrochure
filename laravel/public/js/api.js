// API 유틸리티 함수 (Laravel: window.API_BASE_URL 사용)
const API_BASE_URL = typeof window !== 'undefined' && window.API_BASE_URL
    ? window.API_BASE_URL
    : (() => {
        if (typeof window !== 'undefined' && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
            return (window.location.origin || '') + '/api';
        }
        return (window.location.origin || 'http://localhost:8000') + '/api';
    })();

async function apiCall(endpoint, method = 'GET', data = null) {
    const options = { method, headers: { 'Content-Type': 'application/json' } };
    if (data && (method === 'POST' || method === 'PUT')) options.body = JSON.stringify(data);
    try {
        const response = await fetch(`${API_BASE_URL}${endpoint}`, options);
        if (!response.ok) {
            let errorMessage = `HTTP error! status: ${response.status}`;
            try {
                const ct = response.headers.get('content-type');
                if (ct && ct.includes('application/json')) {
                    const err = await response.json();
                    errorMessage = err.error || errorMessage;
                } else errorMessage = (await response.text()) || errorMessage;
            } catch (_) {}
            if (response.status === 401) errorMessage = '인증 실패: 아이디 또는 비밀번호가 올바르지 않습니다.';
            throw new Error(errorMessage);
        }
        return await response.json();
    } catch (e) {
        console.error('API 호출 오류:', e);
        throw e;
    }
}

const BrochureAPI = {
    getAll: () => apiCall('/brochures'),
    create: (data) => apiCall('/brochures', 'POST', data),
    update: (id, data) => apiCall(`/brochures/${id}`, 'PUT', data),
    delete: (id) => apiCall(`/brochures/${id}`, 'DELETE'),
    updateStock: (id, quantity, date, memo) => apiCall(`/brochures/${id}/stock`, 'PUT', { quantity, date, memo: memo || '' }),
    updateWarehouseStock: (id, quantity, date, memo) => apiCall(`/brochures/${id}/stock-warehouse`, 'PUT', { quantity, date, memo: memo || '' }),
    transferToHq: (id, quantity, date, memo) => apiCall(`/brochures/${id}/transfer-to-hq`, 'PUT', { quantity, date, memo: memo || '' })
};
const ContactAPI = {
    getAll: () => apiCall('/contacts'),
    create: (data) => apiCall('/contacts', 'POST', data),
    update: (id, data) => apiCall(`/contacts/${id}`, 'PUT', data),
    delete: (id) => apiCall(`/contacts/${id}`, 'DELETE')
};
const RequestAPI = {
    getAll: () => apiCall('/requests'),
    create: (data) => apiCall('/requests', 'POST', data),
    update: (id, data) => apiCall(`/requests/${id}`, 'PUT', data),
    addInvoices: (id, invoices) => apiCall(`/requests/${id}/invoices`, 'POST', { invoices }),
    deleteInvoices: (id) => apiCall(`/requests/${id}/invoices`, 'DELETE')
};
const StockHistoryAPI = {
    getAll: () => apiCall('/stock-history'),
    create: (data) => apiCall('/stock-history', 'POST', data)
};
const AdminAPI = {
    login: (username, password) => apiCall('/admin/login', 'POST', { username, password }),
    getAllUsers: () => apiCall('/admin/users'),
    createUser: (username, password) => apiCall('/admin/users', 'POST', { username, password }),
    changePassword: (userId, currentPassword, newPassword) => apiCall(`/admin/users/${userId}/password`, 'PUT', { password: currentPassword, newPassword }),
    deleteUser: (userId) => apiCall(`/admin/users/${userId}`, 'DELETE'),
    resetData: (type) => apiCall('/admin/reset', 'POST', { type })
};

window.BrochureAPI = BrochureAPI;
window.ContactAPI = ContactAPI;
window.RequestAPI = RequestAPI;
window.StockHistoryAPI = StockHistoryAPI;
window.AdminAPI = AdminAPI;
