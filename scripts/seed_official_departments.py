import sqlite3, json

db_file = 'database/database.sqlite'
conn = sqlite3.connect(db_file)
cur = conn.cursor()

# List lengkap user pemohon dan SPV resmi
official_accounts = [
    # PRD
    ('syawal', 'user_PRD', 'PRD', 'PRD Proses', 'Syawal', 'operator', 'produksi'),
    ('zikautsar', 'user_PRD', 'PRD', 'PRD Retail', 'Ahmad Zikautsar', 'operator', 'produksi'),
    ('andi_y', 'Supervisor PRD', 'PRD', 'Produksi', 'Andi Yulianto', 'supervisor', 'produksi'),

    # WRH
    ('alfian', 'user_WRH', 'WRH', 'Gudang', 'Alfian', 'operator', 'warehouse'),
    ('endro', 'Supervisor WRH', 'WRH', 'Warehouse', 'Endro Juniarto', 'supervisor', 'warehouse'),

    # EUT
    ('puput', 'user_EUT', 'EUT', 'Utility', 'Puput Susanto', 'operator', 'eut'),
    ('parhan', 'user_EUT', 'EUT', 'WWTP', 'Ahmad Parhan', 'operator', 'eut'),
    ('miftah', 'user_EUT', 'EUT', 'Otomotif & Maintenance', 'Miftah Hasan Fuadi', 'operator', 'eut'),
    ('muhono_eut', 'Supervisor EUT', 'EUT', 'Engineer Utility', 'Muhono', 'supervisor', 'eut'),
    ('reja', 'Supervisor EUT', 'EUT', 'Engineer Utility', 'Reja Firmansyah', 'supervisor', 'eut'),

    # EPR
    ('feny', 'user_EPR', 'EPR', 'Part Keeper', 'Feny Logina', 'operator', 'epr'),
    ('dicky', 'user_EPR', 'EPR', 'PM Retail', 'Dicky Syaiful', 'operator', 'epr'),
    ('dodi', 'user_EPR', 'EPR', 'PM Proses', 'Dodi Simanjuntak', 'operator', 'epr'),
    ('usep', 'Supervisor EPR', 'EPR', 'Engineering Produksi', 'Usep Hermawan', 'supervisor', 'epr'),

    # QC
    ('intan', 'user_QC', 'QC', 'Kimia & Mikro', 'Intan Purnama', 'operator', 'quality control'),
    ('annisa', 'user_QC', 'QC', 'Retail', 'Annisa Nurfitriana', 'operator', 'quality control'),
    ('yessica', 'user_QC', 'QC', 'RnD (Research)', 'Yessica Tania', 'operator', 'quality control'),
    ('hesti', 'user_QC', 'QC', 'RM (Raw Material)', 'Hesti Kurniati', 'operator', 'quality control'),
    ('fina', 'user_QC', 'QC', 'Quality Control', 'Fina', 'operator', 'quality control'),
    ('veronica', 'Supervisor QC', 'QC', 'Quality Control', 'Veronica Ong', 'supervisor', 'quality control'),

    # GA
    ('tashya', 'user_GA', 'GA', 'General Affairs', 'Tashya Claudea', 'operator', 'hrga'),
    ('nancy', 'Supervisor GA', 'GA', 'General Affairs / HR', 'Nancy Krismawati', 'supervisor', 'hrga'),
    ('yongki', 'Supervisor GA', 'GA', 'General Affairs / HR', 'Yongki Yeremia', 'supervisor', 'hrga'),

    # TMB
    ('dedi_h', 'user_TMB', 'TMB', 'Tambang', 'Dedi Hartono', 'operator', 'tmb')
]

# Insert or update
for username, role, dept, section, fullname, jabatan, departemen in official_accounts:
    # Cek apakah username sudah ada (case-insensitive)
    cur.execute("SELECT id, password FROM users WHERE LOWER(username) = ?", (username.lower(),))
    row = cur.fetchone()
    
    perms = None
    if 'Supervisor' in role or 'Manager' in role:
        perms = json.dumps({'can_sign_approval': True, 'can_create_ejo': True})

    if row:
        cur.execute('''
            UPDATE users 
            SET role = ?, dept = ?, section = ?, jabatan = ?, departemen = ?, access_permissions = ?, is_active = 1
            WHERE id = ?
        ''', (role, dept, section, jabatan, departemen, perms, row[0]))
    else:
        # Generate default password hash: 123456 ($2y$10$eA09m.2pW43G.l7zP26xQ.r47wU2N2XjF215jS4H.Fm3lP71aD6sC)
        cur.execute('''
            INSERT INTO users (
                username, password, role, dept, section, jabatan, departemen, bagian, is_active, access_permissions
            ) VALUES (?, '$2y$10$eA09m.2pW43G.l7zP26xQ.r47wU2N2XjF215jS4H.Fm3lP71aD6sC', ?, ?, ?, ?, ?, ?, 1, ?)
        ''', (username, role, dept, section, jabatan, departemen, section, perms))

conn.commit()

# Print users by department
cur.execute("SELECT dept, role, username, section, jabatan FROM users ORDER BY dept ASC, role ASC, username ASC")
rows = cur.fetchall()
print(f"Total Users: {len(rows)}")
for r in rows:
    print(f"Dept: {r[0]:<5} | Role: {r[1]:<16} | User: {r[2]:<16} | Section: {r[3]:<24} | Jabatan: {r[4]}")

conn.close()
