const sqlite3 = require('sqlite3').verbose();
const fs = require('fs');
const path = require('path');
const bcrypt = require('bcrypt');

// Railway Volume 경로 또는 기본 경로 사용
const dbPath = process.env.DB_PATH || path.join(__dirname, 'brochure.db');
const schemaPath = path.join(__dirname, 'schema.sql');

// 데이터베이스 디렉토리가 없으면 생성 (Railway Volume 사용 시 필요)
const dbDir = path.dirname(dbPath);
if (!fs.existsSync(dbDir)) {
    fs.mkdirSync(dbDir, { recursive: true });
}

// 데이터베이스 초기화
function initDatabase() {
    return new Promise((resolve, reject) => {
        const db = new sqlite3.Database(dbPath, (err) => {
            if (err) {
                console.error('데이터베이스 연결 오류:', err.message);
                reject(err);
                return;
            }
            console.log('데이터베이스에 연결되었습니다.');
        });

        // 스키마 파일 읽기 및 실행
        const schema = fs.readFileSync(schemaPath, 'utf8');
        
        db.exec(schema, (err) => {
            if (err) {
                console.error('스키마 실행 오류:', err.message);
                db.close();
                reject(err);
                return;
            }
            console.log('데이터베이스 스키마가 생성되었습니다.');
            
            // 기본 데이터 삽입
            insertDefaultData(db)
                .then(() => {
                    db.close();
                    console.log('기본 데이터가 삽입되었습니다.');
                    resolve();
                })
                .catch((err) => {
                    db.close();
                    reject(err);
                });
        });
    });
}

// 기본 데이터 삽입
async function insertDefaultData(db) {
    return new Promise((resolve, reject) => {
        // 기본 브로셔 데이터
        const defaultBrochures = [
            { name: 'LittleSEED Play in English', stock: 0 },
            { name: 'Think in English, Speak in English', stock: 0 },
            { name: '어린이 영어교육, 왜 확실한 구어습득이 필요한가?', stock: 0 },
            { name: 'GrapeSEED Elementary', stock: 0 },
            { name: 'Information for Parents', stock: 0 },
            { name: 'LittleSEED at Home Guide', stock: 0 },
            { name: '성공적인 GrapeSEED를 위한 가이드', stock: 0 },
            { name: 'GS Baby', stock: 0 },
            { name: 'GS Online 리플렛', stock: 0 }
        ];

        // 기본 담당자 데이터
        const defaultContacts = [
            { name: 'Addy Kim' },
            { name: 'Peter Kim' },
            { name: 'Ryan Koh' },
            { name: 'Daniel Kim' },
            { name: 'Ron Shin' }
        ];

        db.serialize(() => {
            // 브로셔 데이터 삽입
            const brochureStmt = db.prepare('INSERT OR IGNORE INTO brochures (name, stock) VALUES (?, ?)');
            defaultBrochures.forEach(brochure => {
                brochureStmt.run(brochure.name, brochure.stock);
            });
            brochureStmt.finalize();

            // 담당자 데이터 삽입
            const contactStmt = db.prepare('INSERT OR IGNORE INTO contacts (name) VALUES (?)');
            defaultContacts.forEach(contact => {
                contactStmt.run(contact.name);
            });
            contactStmt.finalize();

            // 기본 관리자 계정 생성 (admin/admin123)
            bcrypt.hash('admin123', 10, (err, hash) => {
                if (err) {
                    console.error('비밀번호 해시 생성 오류:', err);
                    reject(err);
                    return;
                }
                
                db.run('INSERT OR IGNORE INTO admin_users (username, password_hash) VALUES (?, ?)', 
                    ['admin', hash], (err) => {
                        if (err) {
                            console.error('관리자 계정 생성 오류:', err);
                            reject(err);
                        } else {
                            console.log('기본 관리자 계정이 생성되었습니다. (username: admin, password: admin123)');
                            resolve();
                        }
                    });
            });
        });
    });
}

// 실행
if (require.main === module) {
    initDatabase()
        .then(() => {
            console.log('데이터베이스 초기화가 완료되었습니다.');
            process.exit(0);
        })
        .catch((err) => {
            console.error('데이터베이스 초기화 오류:', err);
            process.exit(1);
        });
}

module.exports = { initDatabase };

