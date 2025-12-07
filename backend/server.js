const express = require('express');
const cors = require('cors');
const bodyParser = require('body-parser');
const path = require('path');
const fs = require('fs');
const { runQuery, getQuery, allQuery } = require('./database/db');
const bcrypt = require('bcrypt');
const { initDatabase } = require('./database/init-db');

const app = express();
const PORT = process.env.PORT || 3000;

// 미들웨어
// CORS 설정 - 프로덕션 환경에서 특정 도메인만 허용하도록 설정 가능
const corsOptions = {
    origin: process.env.CORS_ORIGIN ? process.env.CORS_ORIGIN.split(',') : [
        'https://coding0508s.github.io',
        'http://localhost:3000',
        'http://127.0.0.1:3000',
        '*' // 개발 환경용
    ],
    credentials: true
};
app.use(cors(corsOptions));
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// 정적 파일 제공 (프론트엔드 파일) - 상위 디렉토리의 파일들
app.use(express.static(path.join(__dirname, '..')));

// ==================== 브로셔 관리 API ====================

// 모든 브로셔 조회
app.get('/api/brochures', async (req, res) => {
    try {
        const brochures = await allQuery('SELECT * FROM brochures ORDER BY id');
        res.json(brochures);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// 브로셔 추가
app.post('/api/brochures', async (req, res) => {
    try {
        const { name, stock = 0 } = req.body;
        if (!name) {
            return res.status(400).json({ error: '브로셔명은 필수입니다.' });
        }
        
        const result = await runQuery(
            'INSERT INTO brochures (name, stock) VALUES (?, ?)',
            [name, stock]
        );
        res.json({ id: result.lastID, name, stock });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// 브로셔 수정
app.put('/api/brochures/:id', async (req, res) => {
    try {
        const { id } = req.params;
        const { name, stock } = req.body;
        
        await runQuery(
            'UPDATE brochures SET name = ?, stock = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?',
            [name, stock, id]
        );
        res.json({ success: true });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// 브로셔 삭제
app.delete('/api/brochures/:id', async (req, res) => {
    try {
        const { id } = req.params;
        await runQuery('DELETE FROM brochures WHERE id = ?', [id]);
        res.json({ success: true });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// 브로셔 재고 업데이트
app.put('/api/brochures/:id/stock', async (req, res) => {
    try {
        const { id } = req.params;
        const { quantity, date } = req.body;
        
        const brochure = await getQuery('SELECT * FROM brochures WHERE id = ?', [id]);
        if (!brochure) {
            return res.status(404).json({ error: '브로셔를 찾을 수 없습니다.' });
        }
        
        const newStock = brochure.stock + quantity;
        await runQuery(
            'UPDATE brochures SET stock = ?, last_stock_quantity = ?, last_stock_date = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?',
            [newStock, quantity, date, id]
        );
        
        res.json({ success: true, stock: newStock });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// ==================== 담당자 관리 API ====================

// 모든 담당자 조회
app.get('/api/contacts', async (req, res) => {
    try {
        const contacts = await allQuery('SELECT * FROM contacts ORDER BY id');
        res.json(contacts);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// 담당자 추가
app.post('/api/contacts', async (req, res) => {
    try {
        const { name } = req.body;
        if (!name) {
            return res.status(400).json({ error: '담당자명은 필수입니다.' });
        }
        
        const result = await runQuery('INSERT INTO contacts (name) VALUES (?)', [name]);
        res.json({ id: result.lastID, name });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// 담당자 수정
app.put('/api/contacts/:id', async (req, res) => {
    try {
        const { id } = req.params;
        const { name } = req.body;
        
        await runQuery(
            'UPDATE contacts SET name = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?',
            [name, id]
        );
        res.json({ success: true });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// 담당자 삭제
app.delete('/api/contacts/:id', async (req, res) => {
    try {
        const { id } = req.params;
        await runQuery('DELETE FROM contacts WHERE id = ?', [id]);
        res.json({ success: true });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// ==================== 신청 내역 API ====================

// 모든 신청 내역 조회
app.get('/api/requests', async (req, res) => {
    try {
        const requests = await allQuery(`
            SELECT r.*, 
                   GROUP_CONCAT(ri.brochure_id || ':' || ri.brochure_name || ':' || ri.quantity, '|') as items,
                   GROUP_CONCAT(i.invoice_number, '|') as invoices
            FROM requests r
            LEFT JOIN request_items ri ON r.id = ri.request_id
            LEFT JOIN invoices i ON r.id = i.request_id
            GROUP BY r.id
            ORDER BY r.submitted_at DESC
        `);
        
        // 데이터 파싱
        const parsedRequests = requests.map(req => ({
            ...req,
            items: req.items ? req.items.split('|').map(item => {
                const [id, name, qty] = item.split(':');
                return { brochure_id: id, brochure_name: name, quantity: parseInt(qty) };
            }) : [],
            invoices: req.invoices ? req.invoices.split('|') : []
        }));
        
        res.json(parsedRequests);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// 신청 내역 추가
app.post('/api/requests', async (req, res) => {
    try {
        const { date, schoolname, address, phone, contact_id, contact_name, brochures, invoices = [] } = req.body;
        
        if (!date || !schoolname || !address || !phone || !brochures || brochures.length === 0) {
            return res.status(400).json({ error: '필수 필드가 누락되었습니다.' });
        }
        
        // 트랜잭션 시작
        const db = require('./database/db').getDatabase();
        
        return new Promise((resolve, reject) => {
            db.serialize(() => {
                db.run('BEGIN TRANSACTION');
                
                // 신청 내역 추가
                db.run(
                    'INSERT INTO requests (date, schoolname, address, phone, contact_id, contact_name) VALUES (?, ?, ?, ?, ?, ?)',
                    [date, schoolname, address, phone, contact_id, contact_name],
                    function(err) {
                        if (err) {
                            db.run('ROLLBACK');
                            db.close();
                            reject(err);
                            return;
                        }
                        
                        const requestId = this.lastID;
                        const stmt = db.prepare('INSERT INTO request_items (request_id, brochure_id, brochure_name, quantity) VALUES (?, ?, ?, ?)');
                        
                        // 브로셔 항목 추가
                        brochures.forEach(brochure => {
                            stmt.run(requestId, brochure.brochure, brochure.brochureName, brochure.quantity);
                        });
                        stmt.finalize();
                        
                        // 운송장 번호 추가
                        if (invoices && invoices.length > 0) {
                            const invoiceStmt = db.prepare('INSERT INTO invoices (request_id, invoice_number) VALUES (?, ?)');
                            invoices.forEach(invoice => {
                                if (invoice && invoice.trim()) {
                                    invoiceStmt.run(requestId, invoice.trim());
                                }
                            });
                            invoiceStmt.finalize();
                        }
                        
                        db.run('COMMIT', (err) => {
                            db.close();
                            if (err) {
                                reject(err);
                            } else {
                                resolve({ id: requestId });
                            }
                        });
                    }
                );
            });
        })
        .then(result => res.json(result))
        .catch(error => res.status(500).json({ error: error.message }));
        
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// 신청 내역 수정
app.put('/api/requests/:id', async (req, res) => {
    try {
        const { id } = req.params;
        const { date, schoolname, address, phone, contact_id, contact_name, brochures } = req.body;
        
        const db = require('./database/db').getDatabase();
        
        return new Promise((resolve, reject) => {
            db.serialize(() => {
                db.run('BEGIN TRANSACTION');
                
                // 신청 내역 업데이트
                db.run(
                    'UPDATE requests SET date = ?, schoolname = ?, address = ?, phone = ?, contact_id = ?, contact_name = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?',
                    [date, schoolname, address, phone, contact_id, contact_name, id],
                    (err) => {
                        if (err) {
                            db.run('ROLLBACK');
                            db.close();
                            reject(err);
                            return;
                        }
                        
                        // 기존 항목 삭제
                        db.run('DELETE FROM request_items WHERE request_id = ?', [id], (err) => {
                            if (err) {
                                db.run('ROLLBACK');
                                db.close();
                                reject(err);
                                return;
                            }
                            
                            // 새 항목 추가
                            if (brochures && brochures.length > 0) {
                                const stmt = db.prepare('INSERT INTO request_items (request_id, brochure_id, brochure_name, quantity) VALUES (?, ?, ?, ?)');
                                brochures.forEach(brochure => {
                                    stmt.run(id, brochure.brochure, brochure.brochureName, brochure.quantity);
                                });
                                stmt.finalize();
                            }
                            
                            db.run('COMMIT', (err) => {
                                db.close();
                                if (err) {
                                    reject(err);
                                } else {
                                    resolve({ success: true });
                                }
                            });
                        });
                    }
                );
            });
        })
        .then(result => res.json(result))
        .catch(error => res.status(500).json({ error: error.message }));
        
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// 운송장 번호 추가
app.post('/api/requests/:id/invoices', async (req, res) => {
    try {
        const { id } = req.params;
        const { invoices } = req.body;
        
        if (!invoices || !Array.isArray(invoices)) {
            return res.status(400).json({ error: '운송장 번호 배열이 필요합니다.' });
        }
        
        const db = require('./database/db').getDatabase();
        
        return new Promise((resolve, reject) => {
            db.serialize(() => {
                const stmt = db.prepare('INSERT INTO invoices (request_id, invoice_number) VALUES (?, ?)');
                invoices.forEach(invoice => {
                    if (invoice && invoice.trim()) {
                        stmt.run(id, invoice.trim());
                    }
                });
                stmt.finalize((err) => {
                    db.close();
                    if (err) {
                        reject(err);
                    } else {
                        resolve({ success: true });
                    }
                });
            });
        })
        .then(result => res.json(result))
        .catch(error => res.status(500).json({ error: error.message }));
        
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// 운송장 번호 삭제 (신청 내역의 모든 운송장 번호 제거)
app.delete('/api/requests/:id/invoices', async (req, res) => {
    try {
        const { id } = req.params;
        await runQuery('DELETE FROM invoices WHERE request_id = ?', [id]);
        res.json({ success: true });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// ==================== 입출고 내역 API ====================

// 입출고 내역 조회
app.get('/api/stock-history', async (req, res) => {
    try {
        const history = await allQuery('SELECT * FROM stock_history ORDER BY created_at DESC');
        res.json(history);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// 입출고 내역 추가
app.post('/api/stock-history', async (req, res) => {
    try {
        const { type, date, brochure_id, brochure_name, quantity, contact_name, schoolname, before_stock, after_stock } = req.body;
        
        await runQuery(
            'INSERT INTO stock_history (type, date, brochure_id, brochure_name, quantity, contact_name, schoolname, before_stock, after_stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [type, date, brochure_id, brochure_name, quantity, contact_name, schoolname, before_stock, after_stock]
        );
        
        res.json({ success: true });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// ==================== 관리자 인증 API ====================

// 관리자 로그인
app.post('/api/admin/login', async (req, res) => {
    try {
        const { username, password } = req.body;
        
        if (!username || !password) {
            return res.status(400).json({ error: '사용자명과 비밀번호가 필요합니다.' });
        }
        
        const user = await getQuery('SELECT * FROM admin_users WHERE username = ?', [username]);
        
        if (!user) {
            return res.status(401).json({ error: '인증 실패' });
        }
        
        const isValid = await bcrypt.compare(password, user.password_hash);
        
        if (!isValid) {
            return res.status(401).json({ error: '인증 실패' });
        }
        
        res.json({ success: true, username: user.username });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// 데이터베이스 파일 존재 확인 및 자동 초기화
const dbPath = path.join(__dirname, 'database', 'brochure.db');
const dbDir = path.dirname(dbPath);

// 데이터베이스 디렉토리가 없으면 생성
if (!fs.existsSync(dbDir)) {
    fs.mkdirSync(dbDir, { recursive: true });
}

// 서버 시작 함수
function startServer() {
    app.listen(PORT, () => {
        console.log(`서버가 포트 ${PORT}에서 실행 중입니다.`);
        console.log(`API 엔드포인트: http://localhost:${PORT}/api`);
        console.log(`환경: ${process.env.NODE_ENV || 'development'}`);
        if (process.env.NODE_ENV === 'production') {
            console.log('프로덕션 모드 활성화');
        }
    });
}

// 데이터베이스 초기화 확인 및 실행
async function ensureDatabaseInitialized() {
    try {
        // 데이터베이스 파일이 없으면 초기화
        if (!fs.existsSync(dbPath)) {
            console.log('데이터베이스 파일이 없습니다. 초기화를 시작합니다...');
            await initDatabase();
            console.log('데이터베이스 자동 초기화 완료');
            return;
        }

        // 데이터베이스 파일이 있으면 테이블 존재 여부 확인
        const { allQuery } = require('./database/db');
        try {
            // brochures 테이블이 있는지 확인
            await allQuery("SELECT name FROM sqlite_master WHERE type='table' AND name='brochures'");
            const tables = await allQuery("SELECT name FROM sqlite_master WHERE type='table'");
            
            // 필수 테이블이 없으면 초기화
            const requiredTables = ['brochures', 'contacts', 'requests', 'request_items', 'invoices', 'stock_history', 'admin_users'];
            const existingTables = tables.map(t => t.name);
            const missingTables = requiredTables.filter(t => !existingTables.includes(t));
            
            if (missingTables.length > 0) {
                console.log(`필수 테이블이 없습니다: ${missingTables.join(', ')}. 초기화를 시작합니다...`);
                await initDatabase();
                console.log('데이터베이스 자동 초기화 완료');
            }
        } catch (err) {
            // 테이블 확인 중 오류 발생 시 초기화 시도
            console.log('데이터베이스 테이블 확인 중 오류 발생. 초기화를 시작합니다...');
            await initDatabase();
            console.log('데이터베이스 자동 초기화 완료');
        }
    } catch (err) {
        console.error('데이터베이스 초기화 오류:', err);
        // 초기화 실패해도 서버는 시작
    }
}

// 데이터베이스 초기화 확인 후 서버 시작
ensureDatabaseInitialized()
    .then(() => {
        startServer();
    })
    .catch((err) => {
        console.error('데이터베이스 초기화 확인 중 오류:', err);
        startServer();
    });

