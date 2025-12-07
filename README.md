# GS Brochure Management System

GS 브로셔 관리 시스템 - 브로셔 신청, 재고 관리, 운송장 번호 입력을 통합 관리하는 웹 애플리케이션입니다.

## 주요 기능

- 📋 **브로셔 신청**: 기관별 브로셔 신청 및 관리
- 📦 **재고 관리**: 브로셔 재고 입출고 관리
- 🚚 **운송장 관리**: 운송장 번호 입력 및 추적
- 👥 **담당자 관리**: 담당자 정보 관리
- 📊 **통계 및 내역**: 신청 내역 조회 및 입출고 내역 관리
- 📥 **Excel 다운로드**: 신청 내역 및 입출고 내역 Excel 다운로드

## 기술 스택

### 프론트엔드
- HTML5, CSS3, JavaScript (Vanilla JS)
- LocalStorage (클라이언트 캐싱)
- SheetJS (Excel 다운로드)

### 백엔드
- Node.js
- Express.js
- SQLite3
- bcrypt (비밀번호 해싱)

## 시작하기

### 사전 요구사항

- Node.js (v14 이상)
- npm 또는 yarn

### 설치 및 실행

1. **저장소 클론**
   ```bash
   git clone <repository-url>
   cd "GS Brochure management"
   ```

2. **백엔드 설정**
   ```bash
   cd backend  # ⚠️ 중요: 반드시 backend 디렉토리로 이동해야 합니다!
   npm install
   npm run init-db
   ```

3. **백엔드 서버 실행**
   ```bash
   npm start
   ```
   서버가 `http://localhost:3000`에서 실행됩니다.

4. **프론트엔드 접속**
   - 브라우저에서 `requestbrochure.html` 파일을 열거나
   - 로컬 웹 서버를 사용하여 접속

## 프로젝트 구조

```
GS Brochure management/
├── backend/                 # 백엔드 서버
│   ├── database/            # 데이터베이스 관련
│   │   ├── schema.sql       # 데이터베이스 스키마
│   │   ├── init-db.js       # 초기화 스크립트
│   │   └── db.js            # DB 연결 유틸리티
│   ├── server.js            # Express 서버
│   ├── package.json         # 의존성 관리
│   └── .env.example         # 환경 변수 예제
├── js/
│   └── api.js               # API 유틸리티 함수
├── requestbrochure.html     # 브로셔 신청 페이지
├── requestbrochure-list.html # 신청 내역 조회
├── requestbrochure logistics.html # 운송장 번호 입력
├── requestbrochure-completed.html # 완료 내역 조회
├── admin-login.html         # 관리자 로그인
├── admin.html               # 관리자 페이지
└── README.md                # 이 파일
```

## API 엔드포인트

### 브로셔 관리
- `GET /api/brochures` - 브로셔 목록 조회
- `POST /api/brochures` - 브로셔 추가
- `PUT /api/brochures/:id` - 브로셔 수정
- `DELETE /api/brochures/:id` - 브로셔 삭제
- `PUT /api/brochures/:id/stock` - 재고 업데이트

### 담당자 관리
- `GET /api/contacts` - 담당자 목록 조회
- `POST /api/contacts` - 담당자 추가
- `PUT /api/contacts/:id` - 담당자 수정
- `DELETE /api/contacts/:id` - 담당자 삭제

### 신청 내역
- `GET /api/requests` - 신청 내역 조회
- `POST /api/requests` - 신청 내역 추가
- `PUT /api/requests/:id` - 신청 내역 수정
- `POST /api/requests/:id/invoices` - 운송장 번호 추가
- `DELETE /api/requests/:id/invoices` - 운송장 번호 삭제

### 입출고 내역
- `GET /api/stock-history` - 입출고 내역 조회
- `POST /api/stock-history` - 입출고 내역 추가

### 관리자
- `POST /api/admin/login` - 관리자 로그인

## 기본 계정

- **아이디**: admin
- **비밀번호**: admin123

⚠️ **보안**: 프로덕션 환경에서는 반드시 비밀번호를 변경하세요!

## 배포

자세한 배포 가이드는 [DEPLOYMENT.md](./DEPLOYMENT.md)를 참조하세요.

## 라이선스

ISC

## 기여

이슈 및 풀 리퀘스트를 환영합니다.
