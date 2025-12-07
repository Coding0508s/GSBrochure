# GS Brochure Management Backend API

## 설치 및 실행

### 1. 의존성 설치
```bash
npm install
```

### 2. 데이터베이스 초기화
```bash
npm run init-db
```

### 3. 서버 실행
```bash
# 개발 모드 (nodemon 사용)
npm run dev

# 프로덕션 모드
npm start
```

서버는 기본적으로 `http://localhost:3000`에서 실행됩니다.

## API 엔드포인트

### 브로셔 관리
- `GET /api/brochures` - 모든 브로셔 조회
- `POST /api/brochures` - 브로셔 추가
- `PUT /api/brochures/:id` - 브로셔 수정
- `DELETE /api/brochures/:id` - 브로셔 삭제
- `PUT /api/brochures/:id/stock` - 브로셔 재고 업데이트

### 담당자 관리
- `GET /api/contacts` - 모든 담당자 조회
- `POST /api/contacts` - 담당자 추가
- `PUT /api/contacts/:id` - 담당자 수정
- `DELETE /api/contacts/:id` - 담당자 삭제

### 신청 내역 관리
- `GET /api/requests` - 모든 신청 내역 조회
- `POST /api/requests` - 신청 내역 추가
- `PUT /api/requests/:id` - 신청 내역 수정
- `POST /api/requests/:id/invoices` - 운송장 번호 추가
- `DELETE /api/requests/:id/invoices` - 운송장 번호 삭제

### 입출고 내역
- `GET /api/stock-history` - 입출고 내역 조회
- `POST /api/stock-history` - 입출고 내역 추가

### 관리자 인증
- `POST /api/admin/login` - 관리자 로그인

## 기본 관리자 계정
- Username: `admin`
- Password: `admin123`

## 데이터베이스
SQLite 데이터베이스 파일은 `database/brochure.db`에 저장됩니다.

