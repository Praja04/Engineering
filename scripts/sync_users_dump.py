import sqlite3, re, json, subprocess

sql_file = '/mnt/c/Users/user/Downloads/users (6).sql'
db_file = 'database/database.sqlite'

php_script = '''
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\\Contracts\\Console\\Kernel::class);
$kernel->bootstrap();

use App\\Services\\EjoRoleMapperService;

$users = json_decode($argv[1], true);
$results = [];
foreach ($users as $u) {
    $res = EjoRoleMapperService::resolveEjoAttributes(
        $u['username'],
        $u['jabatan'],
        $u['departemen'],
        $u['bagian']
    );
    $results[$u['id']] = $res;
}
echo json_encode($results);
'''

with open(sql_file, 'r', encoding='utf-8') as f:
    sql_text = f.read()

pattern = r"\((\d+),\s*'([^']*)',\s*'([^']*)',\s*([^,]+),\s*([^,]+),\s*'([^']*)',\s*([^,]+),\s*([^,]+),\s*'([^']*)',\s*'([^']*)',\s*([^\)]+)\)"
matches = re.findall(pattern, sql_text)

print(f"Importing & Auto-Injecting {len(matches)} authentic users from SQL Dump via EjoRoleMapperService...")

users_payload = []
user_raw_dict = {}
for m in matches:
    u_id = int(m[0])
    u_name = m[1]
    u_pass = m[2]
    u_created = m[3].strip("'") if m[3] != 'NULL' else None
    u_updated = m[4].strip("'") if m[4] != 'NULL' else None
    u_jabatan = m[5]
    u_image = m[6].strip("'") if m[6] != 'NULL' else None
    u_email = m[7].strip("'") if m[7] != 'NULL' else None
    u_dept = m[8]
    u_bagian = m[9]
    u_nik = m[10].strip("'") if m[10] != 'NULL' else None

    user_raw_dict[u_id] = {
        'id': u_id, 'username': u_name, 'password': u_pass,
        'created_at': u_created, 'updated_at': u_updated,
        'jabatan': u_jabatan, 'image': u_image, 'email': u_email,
        'departemen': u_dept, 'bagian': u_bagian, 'nik': u_nik
    }
    users_payload.append({
        'id': u_id, 'username': u_name, 'jabatan': u_jabatan,
        'departemen': u_dept, 'bagian': u_bagian
    })

# Call PHP to resolve
proc = subprocess.Popen(['/mnt/c/xampp/php/php.exe', '-r', php_script, json.dumps(users_payload)], stdout=subprocess.PIPE, stderr=subprocess.PIPE)
stdout, stderr = proc.communicate()

if proc.returncode != 0:
    print("PHP Error:", stderr.decode('utf-8'))
    exit(1)

mapped_results = json.loads(stdout.decode('utf-8'))

conn = sqlite3.connect(db_file)
cur = conn.cursor()

# Clean & insert
cur.execute("DELETE FROM users")

for u_id, u in user_raw_dict.items():
    mapping = mapped_results.get(str(u_id)) or mapped_results.get(u_id)
    if not mapping:
        continue
    
    cur.execute('''
        INSERT OR REPLACE INTO users (
            id, username, password, created_at, updated_at,
            jabatan, image, email, departemen, bagian, nik,
            role, dept, section, access_permissions, is_active
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
    ''', (
        u['id'], u['username'], u['password'], u['created_at'], u['updated_at'],
        u['jabatan'], u['image'], u['email'], u['departemen'], u['bagian'], u['nik'],
        mapping['role'], mapping['dept'], mapping['section'], mapping['access_permissions']
    ))

# Insert Drafters
cur.execute('''
    INSERT OR REPLACE INTO users (id, username, password, role, dept, section, jabatan, departemen, bagian, is_active, access_permissions)
    VALUES 
    (1000, 'diki', '$2y$10$eA09m.2pW43G.l7zP26xQ.r47wU2N2XjF215jS4H.Fm3lP71aD6sC', 'Drafter', 'ENG', 'Sipil', 'Drafter', 'Engineering', 'Sipil', 1, '{"can_create_ejo":true,"can_upload_drawing":true,"can_edit_ejo":true}'),
    (1001, 'rifan', '$2y$10$eA09m.2pW43G.l7zP26xQ.r47wU2N2XjF215jS4H.Fm3lP71aD6sC', 'Drafter', 'ENG', 'Mekanikal', 'Drafter', 'Engineering', 'Mekanikal', 1, '{"can_create_ejo":true,"can_upload_drawing":true,"can_edit_ejo":true}')
''')

# Insert Server
cur.execute('''
    INSERT OR REPLACE INTO users (id, username, password, role, dept, section, jabatan, departemen, bagian, is_active)
    VALUES (999, 'server', '$2y$10$kTg54d4M7DbmiVNKK.tYuOb7uR/FxKFTFyKhtU8Ce4znOPuc0S9hS', 'Server', 'ENG', 'System Root', 'Root', 'ENG', 'System', 1)
''')

conn.commit()

# Print result
cur.execute("SELECT id, username, role, dept, departemen, bagian, jabatan FROM users ORDER BY id ASC")
rows = cur.fetchall()
print(f"Total Users in Database: {len(rows)}")
for r in rows:
    print(f"ID: {r[0]:<3} | User: {r[1]:<18} | Role EJO: {r[2]:<18} | Dept EJO: {r[3]:<7} | Jabatan: {r[6]:<13} | Dept Asli: {r[4]}")

conn.close()
