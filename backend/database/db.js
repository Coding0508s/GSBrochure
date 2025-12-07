const sqlite3 = require('sqlite3').verbose();
const path = require('path');

const dbPath = path.join(__dirname, 'brochure.db');

// 데이터베이스 연결
function getDatabase() {
    return new sqlite3.Database(dbPath, (err) => {
        if (err) {
            console.error('데이터베이스 연결 오류:', err.message);
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

