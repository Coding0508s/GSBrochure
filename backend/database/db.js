const sqlite3 = require('sqlite3').verbose();
const path = require('path');
const fs = require('fs');

// Railway Volume 경로 또는 기본 경로 사용
// 환경 변수 DB_PATH가 설정되어 있으면 사용, 없으면 기본 경로 사용
const dbPath = process.env.DB_PATH || path.join(__dirname, 'brochure.db');

// 데이터베이스 디렉토리가 없으면 생성 (Railway Volume 사용 시 필요)
const dbDir = path.dirname(dbPath);
if (!fs.existsSync(dbDir)) {
    fs.mkdirSync(dbDir, { recursive: true });
}

// 데이터베이스 연결
function getDatabase() {
    return new sqlite3.Database(dbPath, (err) => {
        if (err) {
            console.error('데이터베이스 연결 오류:', err.message);
        } else {
            console.log('데이터베이스 경로:', dbPath);
        }
    });
}

// Promise 기반 쿼리 실행
function runQuery(query, params = []) {
    return new Promise((resolve, reject) => {
        const db = getDatabase();
        db.run(query, params, function(err) {
            db.close();
            if (err) {
                reject(err);
            } else {
                resolve({ lastID: this.lastID, changes: this.changes });
            }
        });
    });
}

// Promise 기반 데이터 조회
function getQuery(query, params = []) {
    return new Promise((resolve, reject) => {
        const db = getDatabase();
        db.get(query, params, (err, row) => {
            db.close();
            if (err) {
                reject(err);
            } else {
                resolve(row);
            }
        });
    });
}

// Promise 기반 여러 데이터 조회
function allQuery(query, params = []) {
    return new Promise((resolve, reject) => {
        const db = getDatabase();
        db.all(query, params, (err, rows) => {
            db.close();
            if (err) {
                reject(err);
            } else {
                resolve(rows);
            }
        });
    });
}

module.exports = {
    getDatabase,
    runQuery,
    getQuery,
    allQuery
};

