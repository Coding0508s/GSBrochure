// API 유틸리티 함수
// 환경에 따라 API URL 자동 설정
const API_BASE_URL = (() => {
    // 프로덕션 환경 (GitHub Pages 등)
    if (window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
        // 같은 도메인에서 서빙되는 경우 (백엔드가 정적 파일도 서빙)
        // 또는 별도의 백엔드 서버 URL을 설정
        // 예: 'https://your-backend-server.com/api'
        // 같은 도메인인 경우 상대 경로 사용
        return window.location.origin + '/api';
    }
    // 개발 환경
    return 'http://localhost:3000/api';
})();

// 공통 API 호출 함수
async function apiCall(endpoint, method = 'GET', data = null) {
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json'
        }
    };

    if (data && (method === 'POST' || method === 'PUT')) {
        options.body = JSON.stringify(data);
    }

    try {
        const response = await fetch(`${API_BASE_URL}${endpoint}`, options);
        
        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.error || `HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        return result;
    } catch (error) {
        console.error('API 호출 오류:', error);
        throw error;
    }
}

// ==================== 브로셔 API ====================
const BrochureAPI = {
    // 모든 브로셔 조회
    getAll: () => apiCall('/brochures'),
    
    // 브로셔 추가
    create: (data) => apiCall('/brochures', 'POST', data),
    
    // 브로셔 수정
    update: (id, data) => apiCall(`/brochures/${id}`, 'PUT', data),
    
    // 브로셔 삭제
    delete: (id) => apiCall(`/brochures/${id}`, 'DELETE'),
    
    // 재고 업데이트
    updateStock: (id, quantity, date) => apiCall(`/brochures/${id}/stock`, 'PUT', { quantity, date })
};

// ==================== 담당자 API ====================
const ContactAPI = {
    // 모든 담당자 조회
    getAll: () => apiCall('/contacts'),
    
    // 담당자 추가
    create: (data) => apiCall('/contacts', 'POST', data),
    
    // 담당자 수정
    update: (id, data) => apiCall(`/contacts/${id}`, 'PUT', data),
    
    // 담당자 삭제
    delete: (id) => apiCall(`/contacts/${id}`, 'DELETE')
};

// ==================== 신청 내역 API ====================
const RequestAPI = {
    // 모든 신청 내역 조회
    getAll: () => apiCall('/requests'),
    
    // 신청 내역 추가
    create: (data) => apiCall('/requests', 'POST', data),
    
    // 신청 내역 수정
    update: (id, data) => apiCall(`/requests/${id}`, 'PUT', data),
    
    // 운송장 번호 추가
    addInvoices: (id, invoices) => apiCall(`/requests/${id}/invoices`, 'POST', { invoices }),
    
    // 운송장 번호 삭제
    deleteInvoices: (id) => apiCall(`/requests/${id}/invoices`, 'DELETE')
};

// ==================== 입출고 내역 API ====================
const StockHistoryAPI = {
    // 입출고 내역 조회
    getAll: () => apiCall('/stock-history'),
    
    // 입출고 내역 추가
    create: (data) => apiCall('/stock-history', 'POST', data)
};

// ==================== 관리자 API ====================
const AdminAPI = {
    // 로그인
    login: (username, password) => apiCall('/admin/login', 'POST', { username, password })
};

// 전역으로 내보내기
window.BrochureAPI = BrochureAPI;
window.ContactAPI = ContactAPI;
window.RequestAPI = RequestAPI;
window.StockHistoryAPI = StockHistoryAPI;
window.AdminAPI = AdminAPI;

