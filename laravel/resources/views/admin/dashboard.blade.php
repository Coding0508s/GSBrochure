<!DOCTYPE html>
<html class="light" lang="ko">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Brochure Management Dashboard</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📄</text></svg>">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#7f13ec",
                        "background-light": "#f7f6f8",
                        "background-dark": "#191022",
                    },
                    fontFamily: { "display": ["Inter", "sans-serif"] },
                    borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
                },
            },
        }
    </script>
    <script src="{{ asset('js/xlsx.full.min.js') }}"></script>
    <script>window.API_BASE_URL = '{{ url("/api") }}';</script>
    <script src="{{ asset('js/api.js') }}"></script>
</head>
<body class="font-display bg-background-light dark:bg-background-dark text-slate-900 dark:text-white overflow-hidden">
    <div class="flex h-screen w-full">
        <!-- Side Navigation -->
        <div class="w-64 shrink-0 flex flex-col bg-white dark:bg-[#1e1e1e] border-r border-slate-200 dark:border-slate-800 h-full">
            <div class="p-6 pb-2">
                <div class="flex items-center gap-3 mb-8">
                    <div class="rounded-full size-10 bg-primary/20 flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary" style="font-size: 24px;">library_books</span>
                    </div>
                    <div class="flex flex-col">
                        <h1 class="text-slate-900 dark:text-white text-base font-bold leading-normal">BrochureSys</h1>
                        <p class="text-slate-500 dark:text-slate-400 text-xs font-normal">Admin Portal</p>
                    </div>
                </div>
                <nav class="flex flex-col gap-1">
                    <a href="#" data-nav="dashboard" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg bg-primary/10 text-primary transition-colors">
                        <span class="material-symbols-outlined" style="font-size: 24px;">grid_view</span>
                        <span class="text-sm font-medium">대시보드</span>
                    </a>
                    <a href="#" data-nav="inventory" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <span class="material-symbols-outlined" style="font-size: 24px;">inventory_2</span>
                        <span class="text-sm font-medium">재고/브로셔</span>
                    </a>
                    <a href="{{ url('requestbrochure') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <span class="material-symbols-outlined" style="font-size: 24px;">description</span>
                        <span class="text-sm font-medium">브로셔 신청</span>
                    </a>
                    <a href="{{ url('requestbrochure-list') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <span class="material-symbols-outlined" style="font-size: 24px;">campaign</span>
                        <span class="text-sm font-medium">신청 내역</span>
                    </a>
                    <a href="#" data-nav="logistics" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <span class="material-symbols-outlined" style="font-size: 24px;">local_shipping</span>
                        <span class="text-sm font-medium">운송장 입력</span>
                    </a>
                    <a href="#" data-nav="outbound" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <span class="material-symbols-outlined" style="font-size: 24px;">inventory</span>
                        <span class="text-sm font-medium">브로셔 출고 내역</span>
                    </a>
                    <a href="#" data-nav="reports" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <span class="material-symbols-outlined" style="font-size: 24px;">bar_chart</span>
                        <span class="text-sm font-medium">입출고 다운로드</span>
                    </a>
                    <a href="#" data-nav="settings" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <span class="material-symbols-outlined" style="font-size: 24px;">settings</span>
                        <span class="text-sm font-medium">설정</span>
                    </a>
                </nav>
            </div>
            <div class="mt-auto p-6 border-t border-slate-200 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="rounded-full size-9 bg-primary/20 flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary" style="font-size: 20px;">person</span>
                    </div>
                    <div class="flex flex-col">
                        <p class="text-sm font-medium text-slate-900 dark:text-white" id="sidebarUsername">Admin</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">관리자</p>
                    </div>
                </div>
                <button type="button" onclick="logout()" class="mt-3 w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 text-sm font-medium transition-colors">
                    <span class="material-symbols-outlined" style="font-size: 18px;">logout</span>
                    로그아웃
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col h-full overflow-y-auto">
            <header class="w-full px-8 py-6 flex flex-wrap justify-between items-end gap-4 border-b border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-[#1e1e1e]/80 sticky top-0 z-10">
                <div class="flex flex-col gap-1">
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white" id="pageTitle">대시보드 개요</h1>
                    <p class="text-slate-500 dark:text-slate-400 text-sm">GS 브로셔 관리 시스템</p>
                </div>
            </header>

            <div id="alert" class="mx-8 mt-4 hidden rounded-lg px-4 py-3 text-sm" role="alert"></div>

            <!-- Dashboard Section -->
            <section id="section-dashboard" class="content-section px-8 py-6 flex flex-col gap-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" id="statsGrid">
                    <!-- 동적 통계 카드 -->
                </div>
                <div class="flex flex-col lg:flex-row gap-6 flex-1">
                    <div class="flex-[2] bg-white dark:bg-[#1e1e1e] rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">재고 추이</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">품목별 재고 현황</p>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-slate-200 dark:border-slate-700">
                                        <th class="text-left py-2 px-3 text-slate-600 dark:text-slate-400 font-medium">브로셔명</th>
                                        <th class="text-right py-2 px-3 text-slate-600 dark:text-slate-400 font-medium">재고 수량</th>
                                        <th class="text-center py-2 px-3 text-slate-600 dark:text-slate-400 font-medium">상태</th>
                                    </tr>
                                </thead>
                                <tbody id="stockTableBody">
                                    <!-- 품목별 재고 동적 -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="flex-[1] bg-white dark:bg-[#1e1e1e] rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 flex flex-col">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">재고 부족 알림</h3>
                            <a href="#" data-nav="inventory" class="text-primary text-sm font-medium hover:underline">전체 보기</a>
                        </div>
                        <div class="flex flex-col gap-4 overflow-y-auto" id="lowStockAlertsList">
                            <!-- 동적 재고 부족 목록 -->
                        </div>
                    </div>
                </div>
            </section>

            <!-- Inventory Section -->
            <section id="section-inventory" class="content-section px-8 py-6 hidden">
                <div class="flex flex-wrap justify-between items-center gap-4 mb-4">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">브로셔 관리</h2>
                    <div class="flex gap-2">
                        <button type="button" onclick="downloadStockHistory()" class="flex items-center gap-2 px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">입출고 내역 다운로드</button>
                        <button type="button" onclick="openBrochureModal()" class="flex items-center gap-2 px-4 py-2 bg-primary hover:bg-primary/90 rounded-lg text-sm font-medium text-white transition-colors">
                            <span class="material-symbols-outlined" style="font-size: 18px;">add</span>
                            브로셔 추가
                        </button>
                    </div>
                </div>
                <div class="bg-white dark:bg-[#1e1e1e] rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                                    <th class="text-left py-3 px-4 font-medium text-slate-700 dark:text-slate-300">ID</th>
                                    <th class="text-left py-3 px-4 font-medium text-slate-700 dark:text-slate-300">브로셔명</th>
                                    <th class="text-right py-3 px-4 font-medium text-slate-700 dark:text-slate-300">현재 재고</th>
                                    <th class="text-left py-3 px-4 font-medium text-slate-700 dark:text-slate-300">마지막 입고</th>
                                    <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">입고</th>
                                    <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">작업</th>
                                </tr>
                            </thead>
                            <tbody id="brochureTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- 브로셔 출고 내역 -->
            <section id="section-outbound" class="content-section px-8 py-6 hidden">
                <div class="flex flex-wrap justify-between items-center gap-4 mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white">브로셔 출고 내역</h2>
                        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">신청에 따른 출고 처리 내역입니다.</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-[#1e1e1e] rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                                    <th class="text-left py-3 px-4 font-medium text-slate-700 dark:text-slate-300">날짜</th>
                                    <th class="text-left py-3 px-4 font-medium text-slate-700 dark:text-slate-300">브로셔명</th>
                                    <th class="text-right py-3 px-4 font-medium text-slate-700 dark:text-slate-300">수량</th>
                                    <th class="text-left py-3 px-4 font-medium text-slate-700 dark:text-slate-300">담당자</th>
                                    <th class="text-left py-3 px-4 font-medium text-slate-700 dark:text-slate-300">기관명</th>
                                    <th class="text-right py-3 px-4 font-medium text-slate-700 dark:text-slate-300">재고(전→후)</th>
                                    <th class="text-left py-3 px-4 font-medium text-slate-700 dark:text-slate-300">처리일시</th>
                                </tr>
                            </thead>
                            <tbody id="outboundTableBody">
                                <!-- 출고 내역 동적 -->
                            </tbody>
                        </table>
                    </div>
                    <div id="outboundEmpty" class="hidden py-12 text-center text-slate-500 dark:text-slate-400 text-sm">출고 내역이 없습니다.</div>
                </div>
            </section>

            <!-- Reports: just trigger export when nav clicked; section can be minimal -->
            <section id="section-reports" class="content-section px-8 py-6 hidden">
                <p class="text-slate-500 dark:text-slate-400 mb-4">입출고 내역을 Excel로 다운로드합니다.</p>
                <button type="button" onclick="downloadStockHistory()" class="flex items-center gap-2 px-4 py-2 bg-primary hover:bg-primary/90 rounded-lg text-sm font-medium text-white transition-colors">
                    <span class="material-symbols-outlined" style="font-size: 20px;">cloud_download</span>
                    입출고 내역 다운로드
                </button>
            </section>

            <!-- Logistics (운송장 입력) Section -->
            <section id="section-logistics" class="content-section px-8 py-6 hidden">
                <div class="flex flex-wrap justify-between items-end gap-4 mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white">운송장 입력</h2>
                        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">엑셀 다운로드 후 신청된 브로셔의 운송장 번호를 입력하세요.</p>
                    </div>
                    <button type="button" onclick="downloadLogisticsExcel()" class="flex items-center gap-2 px-4 py-2 bg-primary hover:bg-primary/90 rounded-lg text-sm font-medium text-white transition-colors">
                        <span class="material-symbols-outlined" style="font-size: 20px;">cloud_download</span>
                        엑셀 다운로드
                    </button>
                </div>
                <form id="logisticsForm" class="bg-white dark:bg-[#1e1e1e] rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">신청 내역 및 운송장 번호 입력</h3>
                    <div id="rowsContainer"></div>
                    <div class="flex flex-wrap justify-center items-center gap-4 mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                        <span class="text-slate-500 dark:text-slate-400 text-sm" id="paginationInfo"></span>
                        <ul class="flex flex-wrap list-none gap-2 p-0 m-0" id="pagination"></ul>
                    </div>
                </form>
            </section>

            <!-- Settings Section -->
            <section id="section-settings" class="content-section px-8 py-6 hidden">
                <div class="space-y-8">
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-bold text-slate-900 dark:text-white">담당자 관리</h2>
                            <button type="button" onclick="openContactModal()" class="flex items-center gap-2 px-4 py-2 bg-primary hover:bg-primary/90 rounded-lg text-sm font-medium text-white transition-colors">+ 담당자 추가</button>
                        </div>
                        <div class="bg-white dark:bg-[#1e1e1e] rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                                        <th class="text-left py-3 px-4 font-medium text-slate-700 dark:text-slate-300">ID</th>
                                        <th class="text-left py-3 px-4 font-medium text-slate-700 dark:text-slate-300">담당자명</th>
                                        <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">작업</th>
                                    </tr>
                                </thead>
                                <tbody id="contactTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4">관리자 계정 관리</h2>
                        <div class="mb-6 p-4 bg-white dark:bg-[#1e1e1e] rounded-xl border border-slate-200 dark:border-slate-800">
                            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">새 계정 추가</h3>
                            <form id="addAdminForm" class="flex flex-wrap gap-3 items-end">
                                <div class="min-w-[180px]">
                                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">사용자명</label>
                                    <input type="text" id="newUsername" required class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm"/>
                                </div>
                                <div class="min-w-[180px]">
                                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">비밀번호</label>
                                    <input type="password" id="newPassword" required class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm"/>
                                </div>
                                <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary/90 rounded-lg text-sm font-medium text-white transition-colors">계정 추가</button>
                            </form>
                        </div>
                        <div class="bg-white dark:bg-[#1e1e1e] rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                                        <th class="text-left py-3 px-4 font-medium text-slate-700 dark:text-slate-300">ID</th>
                                        <th class="text-left py-3 px-4 font-medium text-slate-700 dark:text-slate-300">사용자명</th>
                                        <th class="text-left py-3 px-4 font-medium text-slate-700 dark:text-slate-300">생성일</th>
                                        <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">작업</th>
                                    </tr>
                                </thead>
                                <tbody id="adminUsersTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Modals (same IDs, Tailwind styled) -->
    <div id="brochureModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-modal="true">
        <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="closeBrochureModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white dark:bg-[#1e1e1e] rounded-xl shadow-xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800">
                <div class="flex justify-between items-center mb-4">
                    <h3 id="brochureModalTitle" class="text-lg font-bold text-slate-900 dark:text-white">브로셔 추가</h3>
                    <button type="button" onclick="closeBrochureModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">&times;</button>
                </div>
                <form id="brochureForm">
                    <input type="hidden" id="brochureId" value="">
                    <div class="form-group mb-4">
                        <label for="brochureName" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">브로셔명</label>
                        <input type="text" id="brochureName" required class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white"/>
                    </div>
                    <div class="form-group mb-4 hidden" id="stockGroup">
                        <label for="brochureStock" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">초기 재고</label>
                        <input type="number" id="brochureStock" min="0" value="0" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white"/>
                    </div>
                    <div class="flex gap-2 justify-end mt-6">
                        <button type="button" onclick="closeBrochureModal()" class="px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">취소</button>
                        <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary/90 rounded-lg text-sm font-medium text-white">저장</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="contactModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-modal="true">
        <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="closeContactModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white dark:bg-[#1e1e1e] rounded-xl shadow-xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800">
                <div class="flex justify-between items-center mb-4">
                    <h3 id="contactModalTitle" class="text-lg font-bold text-slate-900 dark:text-white">담당자 추가</h3>
                    <button type="button" onclick="closeContactModal()" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                <form id="contactForm">
                    <input type="hidden" id="contactId" value="">
                    <div class="form-group mb-4">
                        <label for="contactName" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">담당자명</label>
                        <input type="text" id="contactName" required class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white"/>
                    </div>
                    <div class="flex gap-2 justify-end mt-6">
                        <button type="button" onclick="closeContactModal()" class="px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">취소</button>
                        <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary/90 rounded-lg text-sm font-medium text-white">저장</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="passwordModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-modal="true">
        <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="closePasswordModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white dark:bg-[#1e1e1e] rounded-xl shadow-xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">비밀번호 변경</h3>
                    <button type="button" onclick="closePasswordModal()" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                <form id="changePasswordForm">
                    <input type="hidden" id="changePasswordUserId">
                    <div class="form-group mb-4">
                        <label for="currentPassword" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">현재 비밀번호</label>
                        <input type="password" id="currentPassword" required class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white"/>
                    </div>
                    <div class="form-group mb-4">
                        <label for="newPasswordInput" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">새 비밀번호</label>
                        <input type="password" id="newPasswordInput" required class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white"/>
                    </div>
                    <div class="form-group mb-4">
                        <label for="confirmPassword" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">새 비밀번호 확인</label>
                        <input type="password" id="confirmPassword" required class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white"/>
                    </div>
                    <div class="flex gap-2 justify-end mt-6">
                        <button type="button" onclick="closePasswordModal()" class="px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">취소</button>
                        <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary/90 rounded-lg text-sm font-medium text-white">변경</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="stockModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-modal="true">
        <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="closeStockModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white dark:bg-[#1e1e1e] rounded-xl shadow-xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">입고 처리</h3>
                    <button type="button" onclick="closeStockModal()" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                <form id="stockForm">
                    <input type="hidden" id="stockBrochureId" value="">
                    <div class="form-group mb-4">
                        <p id="stockBrochureName" class="text-sm font-medium text-slate-700 dark:text-slate-300"></p>
                    </div>
                    <div class="form-group mb-4">
                        <label for="stockDate" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">입고 날짜</label>
                        <input type="date" id="stockDate" required class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white"/>
                    </div>
                    <div class="form-group mb-4">
                        <label for="stockQuantity" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">입고 수량</label>
                        <input type="number" id="stockQuantity" min="1" required class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white"/>
                    </div>
                    <div class="flex gap-2 justify-end mt-6">
                        <button type="button" onclick="closeStockModal()" class="px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">취소</button>
                        <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary/90 rounded-lg text-sm font-medium text-white">입고 처리</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="stockEditModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-modal="true">
        <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="closeStockEditModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white dark:bg-[#1e1e1e] rounded-xl shadow-xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">재고 수정</h3>
                    <button type="button" onclick="closeStockEditModal()" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                <form id="stockEditForm">
                    <input type="hidden" id="stockEditBrochureId" value="">
                    <div class="form-group mb-4">
                        <p id="stockEditBrochureName" class="text-sm font-medium text-slate-700 dark:text-slate-300"></p>
                    </div>
                    <div class="form-group mb-4">
                        <label for="stockEditQuantity" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">재고 수량</label>
                        <input type="number" id="stockEditQuantity" min="0" required class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white"/>
                    </div>
                    <div class="form-group mb-4">
                        <label for="stockEditMemo" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">수정 사유 (메모)</label>
                        <textarea id="stockEditMemo" rows="3" placeholder="선택 입력" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white resize-y"></textarea>
                    </div>
                    <div class="flex gap-2 justify-end mt-6">
                        <button type="button" onclick="closeStockEditModal()" class="px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">취소</button>
                        <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary/90 rounded-lg text-sm font-medium text-white">수정</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function checkLogin() {
            const isLoggedIn = sessionStorage.getItem('admin_logged_in');
            if (!isLoggedIn || isLoggedIn !== 'true') {
                window.location.href = '{{ url("admin/login") }}';
                return false;
            }
            const username = sessionStorage.getItem('admin_username');
            if (username) {
                const el = document.getElementById('sidebarUsername');
                if (el) el.textContent = username;
            }
            return true;
        }

        function showSection(sectionId) {
            document.querySelectorAll('.content-section').forEach(el => { el.classList.add('hidden'); });
            const section = document.getElementById('section-' + sectionId);
            if (section) section.classList.remove('hidden');
            document.querySelectorAll('.nav-link').forEach(a => {
                a.classList.remove('bg-primary/10', 'text-primary');
                a.classList.add('text-slate-600', 'dark:text-slate-400');
                if (a.getAttribute('data-nav') === sectionId) {
                    a.classList.add('bg-primary/10', 'text-primary');
                    a.classList.remove('text-slate-600', 'dark:text-slate-400');
                }
            });
            const titles = { dashboard: '대시보드 개요', inventory: '재고/브로셔 관리', reports: '입출고 다운로드', logistics: '운송장 입력', outbound: '브로셔 출고 내역', settings: '설정' };
            const t = document.getElementById('pageTitle');
            if (t && titles[sectionId]) t.textContent = titles[sectionId];
            if (sectionId === 'logistics') loadSavedRequests();
            if (sectionId === 'outbound') loadOutboundHistory();
        }

        document.querySelectorAll('[data-nav]').forEach(a => {
            a.addEventListener('click', function(e) {
                e.preventDefault();
                const sectionId = this.getAttribute('data-nav');
                if (sectionId === 'reports') {
                    downloadStockHistory();
                    return;
                }
                showSection(sectionId);
            });
        });

        function showAlert(message, type) {
            type = type || 'success';
            const alertDiv = document.getElementById('alert');
            alertDiv.className = 'mx-8 mt-4 rounded-lg px-4 py-3 text-sm ' + (type === 'danger' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200' : 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200');
            alertDiv.textContent = message;
            alertDiv.classList.remove('hidden');
            setTimeout(function() { alertDiv.classList.add('hidden'); }, 3000);
        }

        async function loadBrochures() {
            try {
                const brochures = await BrochureAPI.getAll();
                const tbody = document.getElementById('brochureTableBody');
                tbody.innerHTML = '';
                brochures.forEach(brochure => {
                    const stockClass = (brochure.stock || 0) < 10 ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-slate-900 dark:text-white';
                    const lastStockQuantity = brochure.last_stock_quantity || 0;
                    const lastStockDate = brochure.last_stock_date || '-';
                    const row = document.createElement('tr');
                    row.className = 'border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50';
                    row.innerHTML = '<td class="py-3 px-4">' + brochure.id + '</td><td class="py-3 px-4">' + (brochure.name || '') + '</td><td class="py-3 px-4 text-right ' + stockClass + '">' + (brochure.stock || 0) + '권</td><td class="py-3 px-4">' + (lastStockQuantity > 0 ? lastStockQuantity + '권 (' + lastStockDate + ')' : '-') + '</td><td class="py-3 px-4 text-center"><button type="button" onclick="openStockModal(\'' + brochure.id + '\')" class="px-2 py-1 rounded bg-green-600 text-white text-xs font-medium hover:bg-green-700">입고</button></td><td class="py-3 px-4 text-center"><button type="button" onclick="editBrochure(\'' + brochure.id + '\')" class="px-2 py-1 rounded bg-primary text-white text-xs font-medium hover:bg-primary/90 mr-1">수정</button><button type="button" onclick="openStockEditModal(\'' + brochure.id + '\')" class="px-2 py-1 rounded bg-slate-500 text-white text-xs font-medium hover:bg-slate-600 mr-1">재고 수정</button><button type="button" onclick="deleteBrochure(\'' + brochure.id + '\')" class="px-2 py-1 rounded bg-red-600 text-white text-xs font-medium hover:bg-red-700">삭제</button></td>';
                    tbody.appendChild(row);
                });
                updateStats(brochures);
            } catch (err) {
                console.error(err);
                showAlert('브로셔 목록을 불러오는 중 오류가 발생했습니다.', 'danger');
            }
        }

        async function loadContacts() {
            try {
                const contacts = await ContactAPI.getAll();
                const tbody = document.getElementById('contactTableBody');
                tbody.innerHTML = '';
                contacts.forEach(contact => {
                    const row = document.createElement('tr');
                    row.className = 'border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50';
                    row.innerHTML = '<td class="py-3 px-4">' + contact.id + '</td><td class="py-3 px-4">' + (contact.name || '') + '</td><td class="py-3 px-4 text-center"><button type="button" onclick="editContact(\'' + contact.id + '\')" class="px-2 py-1 rounded bg-primary text-white text-xs font-medium hover:bg-primary/90 mr-1">수정</button><button type="button" onclick="deleteContact(\'' + contact.id + '\')" class="px-2 py-1 rounded bg-red-600 text-white text-xs font-medium hover:bg-red-700">삭제</button></td>';
                    tbody.appendChild(row);
                });
            } catch (err) {
                console.error(err);
                showAlert('담당자 목록을 불러오는 중 오류가 발생했습니다.', 'danger');
            }
        }

        function formatDateTime(isoStr) {
            if (!isoStr) return '-';
            try {
                const d = new Date(isoStr);
                if (isNaN(d.getTime())) return isoStr;
                const y = d.getFullYear();
                const m = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                const h = String(d.getHours()).padStart(2, '0');
                const min = String(d.getMinutes()).padStart(2, '0');
                return y + '-' + m + '-' + day + ' ' + h + ':' + min;
            } catch (e) { return isoStr; }
        }

        async function loadOutboundHistory() {
            const tbody = document.getElementById('outboundTableBody');
            const emptyEl = document.getElementById('outboundEmpty');
            if (!tbody) return;
            tbody.innerHTML = '';
            if (emptyEl) emptyEl.classList.add('hidden');
            try {
                const history = await StockHistoryAPI.getAll();
                const outbound = (history || []).filter(function (h) { return h.type === '출고'; });
                if (outbound.length === 0) {
                    if (emptyEl) emptyEl.classList.remove('hidden');
                    return;
                }
                outbound.forEach(function (h) {
                    const row = document.createElement('tr');
                    row.className = 'border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50';
                    row.innerHTML = '<td class="py-3 px-4">' + (h.date || '-') + '</td><td class="py-3 px-4">' + (h.brochure_name || '-') + '</td><td class="py-3 px-4 text-right font-medium">' + (h.quantity ?? '-') + '권</td><td class="py-3 px-4">' + (h.contact_name || '-') + '</td><td class="py-3 px-4">' + (h.schoolname || '-') + '</td><td class="py-3 px-4 text-right">' + (h.before_stock ?? '-') + ' → ' + (h.after_stock ?? '-') + '</td><td class="py-3 px-4 text-slate-500 dark:text-slate-400">' + formatDateTime(h.created_at) + '</td>';
                    tbody.appendChild(row);
                });
            } catch (err) {
                console.error(err);
                showAlert('출고 내역을 불러오는 중 오류가 발생했습니다.', 'danger');
                if (emptyEl) emptyEl.classList.remove('hidden');
            }
        }

        const LOW_STOCK_THRESHOLD = 400;
        function updateStats(brochures) {
            const totalBrochures = brochures.length;
            const totalStock = brochures.reduce((sum, b) => sum + (b.stock || 0), 0);
            const lowStockCount = brochures.filter(b => (b.stock || 0) <= LOW_STOCK_THRESHOLD).length;
            const statsGrid = document.getElementById('statsGrid');
            if (!statsGrid) return;
            statsGrid.innerHTML = '<div class="bg-white dark:bg-[#1e1e1e] rounded-xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm"><div class="flex items-center justify-between mb-2"><p class="text-slate-500 dark:text-slate-400 text-sm font-medium">총 브로셔 종류</p><span class="material-symbols-outlined text-primary bg-primary/10 p-1.5 rounded-lg" style="font-size: 20px;">library_books</span></div><p class="text-2xl font-bold text-slate-900 dark:text-white">' + totalBrochures + '</p></div>' +
                '<div class="bg-white dark:bg-[#1e1e1e] rounded-xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm"><div class="flex items-center justify-between mb-2"><p class="text-slate-500 dark:text-slate-400 text-sm font-medium">재고 부족 항목</p><span class="material-symbols-outlined text-orange-600 bg-orange-100 dark:bg-orange-900/30 p-1.5 rounded-lg" style="font-size: 20px;">warning</span></div><p class="text-2xl font-bold text-slate-900 dark:text-white">' + lowStockCount + '</p><p class="text-sm text-slate-500 dark:text-slate-400 mt-1">' + (lowStockCount > 0 ? '주의 필요' : '정상') + '</p></div>' +
                '<div class="bg-white dark:bg-[#1e1e1e] rounded-xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm"><div class="flex items-center justify-between mb-2"><p class="text-slate-500 dark:text-slate-400 text-sm font-medium">총 재고 수량</p><span class="material-symbols-outlined text-blue-600 bg-blue-100 dark:bg-blue-900/30 p-1.5 rounded-lg" style="font-size: 20px;">swap_horiz</span></div><p class="text-2xl font-bold text-slate-900 dark:text-white">' + totalStock + '권</p></div>';
            updateStockTable();
            var lowList = document.getElementById('lowStockAlertsList');
            if (lowList) {
                var lowItems = brochures.filter(function(b) { return (b.stock || 0) <= LOW_STOCK_THRESHOLD; }).sort(function(a, b) { return (a.stock || 0) - (b.stock || 0); });
                lowList.innerHTML = '';
                lowItems.slice(0, 10).forEach(function(b) {
                    var stock = b.stock || 0;
                    var label = stock <= 0 ? '재고 없음' : (stock + '권 남음');
                    var colorClass = stock <= 0 ? 'text-red-500' : 'text-orange-500';
                    lowList.innerHTML += '<div class="flex items-center gap-3 pb-4 border-b border-slate-100 dark:border-slate-800/50 last:border-0"><div class="rounded-lg size-12 shrink-0 bg-primary/10 flex items-center justify-center"><span class="material-symbols-outlined text-primary" style="font-size: 24px;">menu_book</span></div><div class="flex-1 min-w-0"><p class="text-slate-900 dark:text-white text-sm font-medium truncate">' + (b.name || '') + '</p><p class="text-xs font-medium ' + colorClass + '">' + label + '</p></div><button type="button" onclick="openStockModal(\'' + b.id + '\'); showSection(\'inventory\');" class="size-8 flex items-center justify-center rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-primary hover:text-white transition-colors"><span class="material-symbols-outlined" style="font-size: 18px;">add_shopping_cart</span></button></div>';
                });
                if (lowItems.length === 0) lowList.innerHTML = '<p class="text-slate-500 dark:text-slate-400 text-sm">재고 부족 항목이 없습니다.</p>';
            }
        }

        async function updateStockTable() {
            try {
                const brochures = await BrochureAPI.getAll();
                const tbody = document.getElementById('stockTableBody');
                tbody.innerHTML = '';
                if (brochures.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="3" class="py-6 px-4 text-center text-slate-500 dark:text-slate-400">등록된 브로셔가 없습니다.</td></tr>';
                    return;
                }
                const sorted = brochures.slice().sort((a, b) => (b.stock || 0) - (a.stock || 0));
                sorted.forEach(b => {
                    const stock = b.stock || 0;
                    const statusText = stock <= LOW_STOCK_THRESHOLD ? '재고 부족' : stock < 800 ? '보통' : '충분';
                    const statusColor = stock <= LOW_STOCK_THRESHOLD ? 'text-red-600 dark:text-red-400' : stock < 800 ? 'text-amber-500' : 'text-green-600 dark:text-green-400';
                    const row = document.createElement('tr');
                    row.className = 'border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50';
                    row.innerHTML = '<td class="py-2 px-3">' + (b.name || '') + '</td><td class="py-2 px-3 text-right font-medium">' + stock + '권</td><td class="py-2 px-3 text-center"><span class="font-medium ' + statusColor + '">' + statusText + '</span></td>';
                    tbody.appendChild(row);
                });
            } catch (err) { console.error(err); }
        }

        async function openBrochureModal(id) {
            var modal = document.getElementById('brochureModal');
            var form = document.getElementById('brochureForm');
            var title = document.getElementById('brochureModalTitle');
            var stockGroup = document.getElementById('stockGroup');
            if (id) {
                try {
                    var brochures = await BrochureAPI.getAll();
                    var brochure = brochures.find(function(b) { return b.id == id; });
                    if (brochure) {
                        document.getElementById('brochureId').value = brochure.id;
                        document.getElementById('brochureName').value = brochure.name;
                        title.textContent = '브로셔 수정';
                        stockGroup.classList.add('hidden');
                    }
                } catch (e) { showAlert('브로셔 정보를 불러오는 중 오류가 발생했습니다.', 'danger'); return; }
            } else {
                form.reset();
                document.getElementById('brochureId').value = '';
                title.textContent = '브로셔 추가';
                stockGroup.classList.remove('hidden');
            }
            modal.classList.remove('hidden');
        }
        function closeBrochureModal() {
            document.getElementById('brochureModal').classList.add('hidden');
            document.getElementById('brochureForm').reset();
        }
        document.getElementById('brochureForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            var id = document.getElementById('brochureId').value;
            var name = document.getElementById('brochureName').value;
            var initialStock = parseInt(document.getElementById('brochureStock').value, 10) || 0;
            try {
                if (id) {
                    await BrochureAPI.update(id, { name: name, stock: initialStock });
                    showAlert('브로셔가 수정되었습니다.');
                } else {
                    await BrochureAPI.create({ name: name, stock: initialStock });
                    showAlert('브로셔가 추가되었습니다.');
                }
                await loadBrochures();
                closeBrochureModal();
            } catch (err) { showAlert('브로셔 저장 중 오류: ' + err.message, 'danger'); }
        });

        function editBrochure(id) { openBrochureModal(id); }
        async function deleteBrochure(id) {
            if (!confirm('정말 삭제하시겠습니까?')) return;
            try {
                await BrochureAPI.delete(id);
                await loadBrochures();
                showAlert('브로셔가 삭제되었습니다.');
            } catch (err) { showAlert('브로셔 삭제 중 오류: ' + err.message, 'danger'); }
        }

        async function openStockModal(id) {
            try {
                var brochures = await BrochureAPI.getAll();
                var brochure = brochures.find(function(b) { return b.id == id; });
                if (brochure) {
                    document.getElementById('stockBrochureId').value = id;
                    document.getElementById('stockBrochureName').textContent = '브로셔명: ' + brochure.name;
                    document.getElementById('stockDate').value = new Date().toISOString().slice(0, 10);
                    document.getElementById('stockQuantity').value = 1;
                    document.getElementById('stockModal').classList.remove('hidden');
                }
            } catch (e) { showAlert('브로셔 정보를 불러오는 중 오류가 발생했습니다.', 'danger'); }
        }
        function closeStockModal() {
            document.getElementById('stockModal').classList.add('hidden');
            document.getElementById('stockForm').reset();
        }
        document.getElementById('stockForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            var id = document.getElementById('stockBrochureId').value;
            var quantity = parseInt(document.getElementById('stockQuantity').value, 10) || 0;
            var date = document.getElementById('stockDate').value;
            if (quantity <= 0 || !date) { showAlert('입고 수량과 날짜를 확인해주세요.', 'danger'); return; }
            try {
                var brochures = await BrochureAPI.getAll();
                var brochure = brochures.find(function(b) { return b.id == id; });
                if (!brochure) { showAlert('브로셔를 찾을 수 없습니다.', 'danger'); return; }
                var beforeStock = brochure.stock || 0;
                await BrochureAPI.updateStock(id, quantity, date);
                await StockHistoryAPI.create({ type: '입고', date: date, brochure_id: id, brochure_name: brochure.name, quantity: quantity, contact_name: '관리자', before_stock: beforeStock, after_stock: beforeStock + quantity });
                await loadBrochures();
                showAlert(quantity + '권 입고되었습니다. (입고일: ' + date + ')');
                closeStockModal();
            } catch (err) { showAlert('입고 처리 중 오류: ' + err.message, 'danger'); }
        });

        async function openStockEditModal(id) {
            try {
                var brochures = await BrochureAPI.getAll();
                var brochure = brochures.find(function(b) { return b.id == id; });
                if (brochure) {
                    document.getElementById('stockEditBrochureId').value = id;
                    document.getElementById('stockEditBrochureName').textContent = '브로셔명: ' + brochure.name;
                    document.getElementById('stockEditQuantity').value = brochure.stock || 0;
                    document.getElementById('stockEditMemo').value = '';
                    document.getElementById('stockEditModal').classList.remove('hidden');
                }
            } catch (e) { showAlert('브로셔 정보를 불러오는 중 오류가 발생했습니다.', 'danger'); }
        }
        function closeStockEditModal() {
            document.getElementById('stockEditModal').classList.add('hidden');
            document.getElementById('stockEditForm').reset();
            document.getElementById('stockEditMemo').value = '';
        }
        document.getElementById('stockEditForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            var id = document.getElementById('stockEditBrochureId').value;
            var newQuantity = parseInt(document.getElementById('stockEditQuantity').value, 10) || 0;
            var memo = (document.getElementById('stockEditMemo') && document.getElementById('stockEditMemo').value) ? document.getElementById('stockEditMemo').value.trim() : '';
            if (newQuantity < 0) { showAlert('재고 수량은 0 이상이어야 합니다.', 'danger'); return; }
            try {
                var brochures = await BrochureAPI.getAll();
                var brochure = brochures.find(function(b) { return b.id == id; });
                if (!brochure) { showAlert('브로셔를 찾을 수 없습니다.', 'danger'); return; }
                var diff = newQuantity - (brochure.stock || 0);
                var dateStr = new Date().toISOString().slice(0, 10);
                await BrochureAPI.updateStock(id, diff, dateStr, memo);
                await loadBrochures();
                showAlert('재고가 수정되었습니다.');
                closeStockEditModal();
            } catch (err) { showAlert('재고 수정 중 오류: ' + err.message, 'danger'); }
        });

        async function openContactModal(id) {
            var modal = document.getElementById('contactModal');
            var form = document.getElementById('contactForm');
            var title = document.getElementById('contactModalTitle');
            if (id) {
                try {
                    var contacts = await ContactAPI.getAll();
                    var contact = contacts.find(function(c) { return c.id == id; });
                    if (contact) {
                        document.getElementById('contactId').value = contact.id;
                        document.getElementById('contactName').value = contact.name;
                        title.textContent = '담당자 수정';
                    }
                } catch (e) { showAlert('담당자 정보를 불러오는 중 오류가 발생했습니다.', 'danger'); return; }
            } else {
                form.reset();
                document.getElementById('contactId').value = '';
                title.textContent = '담당자 추가';
            }
            modal.classList.remove('hidden');
        }
        function closeContactModal() {
            document.getElementById('contactModal').classList.add('hidden');
            document.getElementById('contactForm').reset();
        }
        document.getElementById('contactForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            var id = document.getElementById('contactId').value;
            var name = document.getElementById('contactName').value;
            try {
                if (id) {
                    await ContactAPI.update(id, { name: name });
                    showAlert('담당자가 수정되었습니다.');
                } else {
                    await ContactAPI.create({ name: name });
                    showAlert('담당자가 추가되었습니다.');
                }
                await loadContacts();
                closeContactModal();
            } catch (err) { showAlert('담당자 저장 중 오류: ' + err.message, 'danger'); }
        });
        function editContact(id) { openContactModal(id); }
        async function deleteContact(id) {
            if (!confirm('정말 삭제하시겠습니까?')) return;
            try {
                await ContactAPI.delete(id);
                await loadContacts();
                showAlert('담당자가 삭제되었습니다.');
            } catch (err) { showAlert('담당자 삭제 중 오류: ' + err.message, 'danger'); }
        }

        async function downloadStockHistory() {
            if (typeof XLSX === 'undefined') { showAlert('엑셀 라이브러리를 불러오지 못했습니다. 페이지를 새로고침한 후 다시 시도해주세요.', 'danger'); return; }
            try {
                var history = await StockHistoryAPI.getAll();
                if (!Array.isArray(history)) history = [];
                if (history.length === 0) { showAlert('다운로드할 입출고 내역이 없습니다.', 'danger'); return; }
                var excelData = [['구분', '날짜', '브로셔명', '수량', '담당자', '기관명', '이전 재고', '이후 재고', '처리 시간']];
                history.forEach(function(item) {
                    excelData.push([item.type || '', item.date || '', item.brochure_name || '', item.quantity || 0, item.contact_name || '', item.schoolname || '', item.before_stock || 0, item.after_stock || 0, item.created_at ? new Date(item.created_at).toLocaleString('ko-KR') : '']);
                });
                var wb = XLSX.utils.book_new();
                var ws = XLSX.utils.aoa_to_sheet(excelData);
                ws['!cols'] = [{ wch: 10 }, { wch: 12 }, { wch: 30 }, { wch: 10 }, { wch: 15 }, { wch: 20 }, { wch: 12 }, { wch: 12 }, { wch: 20 }];
                XLSX.utils.book_append_sheet(wb, ws, '입출고 내역');
                var dateStr = new Date().toISOString().slice(0, 10).replace(/-/g, '');
                XLSX.writeFile(wb, '입출고내역_' + dateStr + '.xlsx');
                showAlert('입출고 내역이 다운로드되었습니다.');
            } catch (err) { showAlert('입출고 내역 다운로드 중 오류: ' + err.message, 'danger'); }
        }

        function logout() {
            sessionStorage.removeItem('admin_logged_in');
            sessionStorage.removeItem('admin_username');
            window.location.href = '{{ url("admin/login") }}';
        }

        async function loadAdminUsers() {
            try {
                var users = await AdminAPI.getAllUsers();
                var tbody = document.getElementById('adminUsersTableBody');
                tbody.innerHTML = '';
                users.forEach(function(user) {
                    var row = document.createElement('tr');
                    row.className = 'border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50';
                    row.innerHTML = '<td class="py-3 px-4">' + user.id + '</td><td class="py-3 px-4">' + (user.username || '') + '</td><td class="py-3 px-4">' + (user.created_at ? new Date(user.created_at).toLocaleDateString('ko-KR') : '') + '</td><td class="py-3 px-4 text-center"><button type="button" onclick="openPasswordModal(' + user.id + ')" class="px-2 py-1 rounded bg-primary text-white text-xs font-medium hover:bg-primary/90 mr-1">비밀번호 변경</button><button type="button" onclick="deleteAdminUser(' + user.id + ')" ' + (users.length <= 1 ? 'disabled' : '') + ' class="px-2 py-1 rounded bg-red-600 text-white text-xs font-medium hover:bg-red-700">삭제</button></td>';
                    tbody.appendChild(row);
                });
            } catch (err) {
                console.error(err);
                showAlert('관리자 계정을 불러오는 중 오류가 발생했습니다.', 'danger');
            }
        }
        document.getElementById('addAdminForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            var username = document.getElementById('newUsername').value;
            var password = document.getElementById('newPassword').value;
            try {
                var result = await AdminAPI.createUser(username, password);
                if (result.success) {
                    showAlert('계정이 추가되었습니다.');
                    document.getElementById('addAdminForm').reset();
                    await loadAdminUsers();
                } else { showAlert(result.error || '계정 추가 중 오류가 발생했습니다.', 'danger'); }
            } catch (err) { showAlert('계정 추가 중 오류: ' + err.message, 'danger'); }
        });
        function openPasswordModal(userId) {
            document.getElementById('changePasswordUserId').value = userId;
            document.getElementById('passwordModal').classList.remove('hidden');
        }
        function closePasswordModal() {
            document.getElementById('passwordModal').classList.add('hidden');
            document.getElementById('changePasswordForm').reset();
        }
        document.getElementById('changePasswordForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            var userId = document.getElementById('changePasswordUserId').value;
            var currentPassword = document.getElementById('currentPassword').value;
            var newPassword = document.getElementById('newPasswordInput').value;
            var confirmPassword = document.getElementById('confirmPassword').value;
            if (newPassword !== confirmPassword) { showAlert('새 비밀번호가 일치하지 않습니다.', 'danger'); return; }
            try {
                var result = await AdminAPI.changePassword(userId, currentPassword, newPassword);
                if (result.success) { showAlert('비밀번호가 변경되었습니다.'); closePasswordModal(); }
                else { showAlert(result.error || '비밀번호 변경 중 오류가 발생했습니다.', 'danger'); }
            } catch (err) { showAlert('비밀번호 변경 중 오류: ' + err.message, 'danger'); }
        });
        async function deleteAdminUser(userId) {
            if (!confirm('정말 이 계정을 삭제하시겠습니까?')) return;
            try {
                var result = await AdminAPI.deleteUser(userId);
                if (result.success) { showAlert('계정이 삭제되었습니다.'); await loadAdminUsers(); }
                else { showAlert(result.error || '계정 삭제 중 오류가 발생했습니다.', 'danger'); }
            } catch (err) { showAlert('계정 삭제 중 오류: ' + err.message, 'danger'); }
        }

        // ----- 운송장 입력 (logistics) 섹션 -----
        var logisticsRowCount = 0, logisticsCurrentPage = 1, logisticsItemsPerPage = 10, allPendingRequests = [];
        async function addRowFromData(request, requestIndex, itemIndex, requestId) {
            logisticsRowCount++;
            var rowsContainer = document.getElementById('rowsContainer');
            if (!rowsContainer) return;
            var rowDiv = document.createElement('div');
            rowDiv.className = 'border-2 border-primary/30 rounded-xl p-4 mb-4 bg-white dark:bg-slate-800/50 dark:border-slate-700';
            rowDiv.id = 'row-' + logisticsRowCount;
            rowDiv.dataset.requestIndex = requestIndex;
            rowDiv.dataset.itemIndex = itemIndex;
            rowDiv.dataset.requestId = requestId;
            rowDiv.innerHTML =
                '<div class="flex flex-wrap gap-4 items-end mb-4 pb-3 border-b border-slate-200 dark:border-slate-700">' +
                '<div class="min-w-[120px]"><label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">날짜</label><input type="date" id="logistics-date-' + logisticsRowCount + '" value="' + (request.date || '') + '" disabled class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 cursor-not-allowed text-sm"></div>' +
                '<div class="flex-1 min-w-[200px]"><label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">기관명</label><input type="text" id="logistics-schoolname-' + logisticsRowCount + '" value="' + (request.schoolname || '') + '" disabled class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-medium cursor-not-allowed text-sm"></div>' +
                '<div class="min-w-[120px]"><span class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">담당자</span><span class="block text-sm text-slate-900 dark:text-white">' + (request.contactName || request.contact || '-') + '</span></div></div>' +
                '<div class="flex flex-wrap gap-4 mb-4"><div class="flex-1 min-w-[200px]"><label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">주소</label><input type="text" id="logistics-address-' + logisticsRowCount + '" value="' + (request.address || '') + '" disabled class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 cursor-not-allowed text-sm"></div>' +
                '<div class="min-w-[140px]"><label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">전화번호</label><input type="tel" id="logistics-phone-' + logisticsRowCount + '" value="' + (request.phone || '') + '" disabled class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 cursor-not-allowed text-sm"></div></div>' +
                '<div class="mb-4"><label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">브로셔 신청 내역</label><div id="logistics-brochure-list-' + logisticsRowCount + '" class="px-3 py-2 rounded-lg bg-slate-50 dark:bg-slate-800/50 text-sm text-slate-900 dark:text-white">' + (request.brochures && request.brochures.length > 0 ? request.brochures.map(function(b) { return (b.brochureName || '') + ' - ' + (b.quantity || 0) + '권'; }).join('<br>') : '브로셔 정보 없음') + '</div></div>' +
                '<div class="mb-4"><label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">운송장 번호</label><div class="flex flex-wrap gap-2 items-end" id="invoice-container-' + logisticsRowCount + '"></div><button type="button" onclick="addLogisticsInvoiceField(' + logisticsRowCount + ')" class="mt-2 flex items-center gap-1 px-3 py-1.5 rounded-lg bg-green-600 hover:bg-green-700 text-white text-sm font-medium">+ 운송장 번호 추가</button></div>' +
                '<div class="flex justify-end"><button type="button" onclick="saveSingleInvoice(' + logisticsRowCount + ',' + requestIndex + ',' + itemIndex + ')" class="px-4 py-2 bg-primary hover:bg-primary/90 rounded-lg text-sm font-medium text-white">저장</button></div>';
            rowsContainer.appendChild(rowDiv);
            await new Promise(function(r) { setTimeout(r, 0); });
            var rc = logisticsRowCount;
            if (request.invoices && request.invoices.length > 0) {
                request.invoices.forEach(function(inv, idx) {
                    addLogisticsInvoiceField(rc, idx === 0);
                    var inp = document.querySelector('#invoice-container-' + rc + ' input[type="text"]:last-of-type');
                    if (inp) inp.value = inv;
                });
            } else { addLogisticsInvoiceField(rc, true); }
        }
        function addLogisticsInvoiceField(rowId, isDefault) {
            var container = document.getElementById('invoice-container-' + rowId);
            if (!container) return;
            var invoiceCount = container.querySelectorAll('.invoice-group').length;
            var invoiceId = 'invoice-' + rowId + '-' + (invoiceCount + 1);
            var invoiceGroup = document.createElement('div');
            invoiceGroup.className = 'invoice-group flex gap-2 items-end';
            invoiceGroup.id = 'invoice-group-' + invoiceId;
            var input = document.createElement('input');
            input.type = 'text';
            input.id = invoiceId;
            input.placeholder = '송장번호를 입력하세요';
            input.className = 'w-48 min-w-[120px] px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm font-medium';
            invoiceGroup.appendChild(input);
            if (!isDefault) {
                var deleteBtn = document.createElement('button');
                deleteBtn.type = 'button';
                deleteBtn.className = 'px-2 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-medium';
                deleteBtn.textContent = '삭제';
                deleteBtn.onclick = function() { invoiceGroup.remove(); };
                invoiceGroup.appendChild(deleteBtn);
            }
            container.appendChild(invoiceGroup);
        }
        function collectLogisticsInvoiceFields(rowId) {
            var container = document.getElementById('invoice-container-' + rowId);
            if (!container) return [];
            var invoices = [];
            container.querySelectorAll('input[type="text"]').forEach(function(input) { if (input.value.trim()) invoices.push(input.value.trim()); });
            return invoices;
        }
        async function saveSingleInvoice(rowId, requestIndex, itemIndex) {
            var invoices = collectLogisticsInvoiceFields(rowId).filter(function(inv) { return inv && inv.trim() !== ''; });
            if (invoices.length === 0) { showAlert('운송장 번호를 입력해주세요.', 'danger'); return; }
            try {
                var row = document.getElementById('row-' + rowId);
                var requestId = row ? row.dataset.requestId : null;
                if (!requestId) { showAlert('요청 ID를 찾을 수 없습니다.', 'danger'); return; }
                await RequestAPI.addInvoices(requestId, invoices);
                showAlert('운송장 번호가 저장되었습니다!', 'success');
                setTimeout(function() { loadSavedRequests(); }, 500);
            } catch (err) { showAlert('운송장 번호 저장 중 오류: ' + (err.message || ''), 'danger'); }
        }
        async function loadSavedRequests() {
            try {
                var allRequests = await RequestAPI.getAll();
                var rowsContainer = document.getElementById('rowsContainer');
                if (!rowsContainer) return;
                rowsContainer.innerHTML = '';
                if (allRequests.length === 0) {
                    rowsContainer.innerHTML = '<p class="text-center text-slate-500 dark:text-slate-400 py-8">저장된 신청 내역이 없습니다.</p>';
                    var pag = document.getElementById('pagination');
                    var pagInfo = document.getElementById('paginationInfo');
                    if (pag) pag.innerHTML = '';
                    if (pagInfo) pagInfo.textContent = '';
                    return;
                }
                allPendingRequests = [];
                allRequests.forEach(function(req) {
                    if (!req.invoices || req.invoices.length === 0 || req.invoices.every(function(inv) { return !inv || (typeof inv === 'string' && inv.trim() === ''); })) {
                        var request = { date: req.date, schoolname: req.schoolname, address: req.address, phone: req.phone, contact: req.contact_id, contactName: req.contact_name, brochures: (req.items || []).map(function(item) { return { brochure: item.brochure_id, brochureName: item.brochure_name, quantity: item.quantity }; }), invoices: req.invoices || [] };
                        allPendingRequests.push({ request: request, requestId: req.id });
                    }
                });
                if (allPendingRequests.length === 0) {
                    rowsContainer.innerHTML = '<p class="text-center text-slate-500 dark:text-slate-400 py-8">운송장 번호 입력이 필요한 신청 내역이 없습니다.</p>';
                    var pag = document.getElementById('pagination');
                    var pagInfo = document.getElementById('paginationInfo');
                    if (pag) pag.innerHTML = '';
                    if (pagInfo) pagInfo.textContent = '';
                    return;
                }
                logisticsCurrentPage = 1;
                displayPagedRequests();
            } catch (err) { console.error(err); showAlert('신청 내역을 불러오는 중 오류가 발생했습니다.', 'danger'); }
        }
        async function displayPagedRequests() {
            var rowsContainer = document.getElementById('rowsContainer');
            if (!rowsContainer) return;
            rowsContainer.innerHTML = '';
            var totalItems = allPendingRequests.length;
            var totalPages = Math.ceil(totalItems / logisticsItemsPerPage);
            var startIndex = (logisticsCurrentPage - 1) * logisticsItemsPerPage;
            var pageItems = allPendingRequests.slice(startIndex, startIndex + logisticsItemsPerPage);
            for (var i = 0; i < pageItems.length; i++) {
                var item = pageItems[i];
                await addRowFromData(item.request, startIndex + i, 0, item.requestId);
            }
            var pagination = document.getElementById('pagination');
            var paginationInfo = document.getElementById('paginationInfo');
            if (pagination) pagination.innerHTML = '';
            if (paginationInfo) paginationInfo.textContent = totalPages <= 1 ? '총 ' + totalItems + '개' : '총 ' + totalItems + '개 중 ' + (startIndex + 1) + '-' + Math.min(startIndex + logisticsItemsPerPage, totalItems) + '개 표시';
            if (totalPages <= 1) return;
            var prevLi = document.createElement('li');
            prevLi.innerHTML = '<button type="button" onclick="goToLogisticsPage(' + (logisticsCurrentPage - 1) + ')" ' + (logisticsCurrentPage === 1 ? 'disabled' : '') + ' class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-sm font-medium ' + (logisticsCurrentPage === 1 ? 'text-slate-400 cursor-not-allowed' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800') + '">이전</button>';
            pagination.appendChild(prevLi);
            var startPage = Math.max(1, logisticsCurrentPage - 2), endPage = Math.min(totalPages, logisticsCurrentPage + 2);
            if (startPage > 1) {
                var li = document.createElement('li');
                li.innerHTML = '<button type="button" onclick="goToLogisticsPage(1)" class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">1</button>';
                pagination.appendChild(li);
                if (startPage > 2) { var d = document.createElement('li'); d.innerHTML = '<span class="px-2 text-slate-400">...</span>'; pagination.appendChild(d); }
            }
            for (var p = startPage; p <= endPage; p++) {
                var li = document.createElement('li');
                li.innerHTML = '<button type="button" onclick="goToLogisticsPage(' + p + ')" class="px-3 py-1.5 rounded-lg border text-sm font-medium ' + (p === logisticsCurrentPage ? 'bg-primary border-primary text-white' : 'border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800') + '">' + p + '</button>';
                pagination.appendChild(li);
            }
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) { var d = document.createElement('li'); d.innerHTML = '<span class="px-2 text-slate-400">...</span>'; pagination.appendChild(d); }
                var li = document.createElement('li');
                li.innerHTML = '<button type="button" onclick="goToLogisticsPage(' + totalPages + ')" class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">' + totalPages + '</button>';
                pagination.appendChild(li);
            }
            var nextLi = document.createElement('li');
            nextLi.innerHTML = '<button type="button" onclick="goToLogisticsPage(' + (logisticsCurrentPage + 1) + ')" ' + (logisticsCurrentPage === totalPages ? 'disabled' : '') + ' class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-sm font-medium ' + (logisticsCurrentPage === totalPages ? 'text-slate-400 cursor-not-allowed' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800') + '">다음</button>';
            pagination.appendChild(nextLi);
        }
        async function goToLogisticsPage(page) {
            var totalPages = Math.ceil(allPendingRequests.length / logisticsItemsPerPage);
            if (page < 1 || page > totalPages) return;
            logisticsCurrentPage = page;
            await displayPagedRequests();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        function downloadLogisticsExcel() {
            if (typeof XLSX === 'undefined') { showAlert('엑셀 라이브러리를 불러오지 못했습니다. 페이지를 새로고침한 후 다시 시도해주세요.', 'danger'); return; }
            if (allPendingRequests.length === 0) { showAlert('다운로드할 신청 내역이 없습니다.', 'danger'); return; }
            var excelData = [['배송메세지1', '받는분성명', '받는분전화번호', '받는분주소(전체, 분할)', '내품코드', '내품명', '내품수량', '박스타입', '운임구분', '운송장번호', '']];
            allPendingRequests.forEach(function(item) {
                var request = item.request;
                if (request.brochures && request.brochures.length > 0) {
                    request.brochures.forEach(function(brochure) {
                        excelData.push(['브로셔', request.schoolname || '', request.phone || '', request.address || '', 'Brochure', brochure.brochureName || '', brochure.quantity || '', '', '', request.invoices && request.invoices.length > 0 ? request.invoices.join(', ') : '', '']);
                    });
                } else {
                    excelData.push(['브로셔', request.schoolname || '', request.phone || '', request.address || '', 'Brochure', '', '', '', '', request.invoices && request.invoices.length > 0 ? request.invoices.join(', ') : '', '']);
                }
            });
            var wb = XLSX.utils.book_new();
            var ws = XLSX.utils.aoa_to_sheet(excelData);
            ws['!cols'] = [{ wch: 15 }, { wch: 15 }, { wch: 18 }, { wch: 40 }, { wch: 12 }, { wch: 30 }, { wch: 12 }, { wch: 12 }, { wch: 12 }, { wch: 20 }, { wch: 10 }];
            XLSX.utils.book_append_sheet(wb, ws, '신청 내역');
            var dateStr = new Date().toISOString().slice(0, 10).replace(/-/g, '');
            XLSX.writeFile(wb, '브로셔_신청내역_' + dateStr + '.xlsx');
            showAlert('엑셀 파일이 다운로드되었습니다.', 'success');
        }
        var logisticsFormEl = document.getElementById('logisticsForm');
        if (logisticsFormEl) logisticsFormEl.addEventListener('submit', function(e) { e.preventDefault(); showAlert('각 건마다 개별 저장 버튼을 사용해주세요.', 'danger'); });

        window.addEventListener('DOMContentLoaded', async function() {
            if (!checkLogin()) return;
            await loadBrochures();
            await loadContacts();
            await loadAdminUsers();
            var params = new URLSearchParams(window.location.search);
            if (params.get('section') === 'logistics') showSection('logistics');
        });
    </script>
</body>
</html>
