# TESTING PLAN — SIAKMAN (Dashboard Al-Iman)
> Blueprint testing menggunakan PEST PHP sebelum implementasi kode test.

---

## BAGIAN 1: PROJECT SUMMARY

### Hasil Scan Project

| Item | Temuan |
|---|---|
| **Total Model** | 11 model |
| **Total Komponen Livewire** | 15 komponen |
| **Total Route (web)** | 21 route (web) |
| **Total Route (api)** | 0 route |
| **Total Tabel DB** | 12 tabel |
| **Ada Service Class** | Tidak |
| **Ada Custom Controller** | Tidak (hanya base Controller.php) |
| **Halaman dengan Alpine.js** | 15+ halaman |
| **Pest versi** | ^4.1 (sudah terinstall) |
| **pest-plugin-laravel** | ^4.0 (sudah terinstall) |
| **pest-plugin-livewire** | Belum terinstall ⚠️ |
| **Factories yang ada** | 1 (UserFactory) — 10 model belum punya factory |

### Daftar Model
1. `User` — relasi: hasOne ProfilMurid, hasOne ProfilGuru
2. `ProfilMurid` — relasi: belongsTo User, hasMany Absensi, hasMany Nilai, belongsToMany Kelas (pivot: tahun_ajaran)
3. `ProfilGuru` — relasi: belongsTo User, hasMany TeachingSchedule, hasMany Kelas (wali), hasMany Nilai
4. `Kelas` — relasi: belongsTo ProfilGuru (wali), belongsToMany ProfilMurid, hasMany TeachingSchedule, hasMany Nilai
5. `Subject` — relasi: hasMany TeachingSchedule, hasMany Nilai
6. `TeachingSchedule` — relasi: belongsTo ProfilGuru, Subject, Kelas; hasMany PertemuanKelas
7. `PertemuanKelas` — relasi: belongsTo TeachingSchedule; hasMany Absensi
8. `Absensi` — relasi: belongsTo PertemuanKelas, ProfilMurid
9. `Nilai` — relasi: belongsTo ProfilMurid, ProfilGuru, Subject, Kelas; unique constraint 6 kolom
10. `AcademicEvent` — scope: `scopeUpcoming()`; static: `defaultColor()`
11. `SchoolSetting` — static: `get()`, `set()`

### Daftar Komponen Livewire
| # | Komponen | Role Akses | Computed Properties | Fitur Kritis |
|---|---|---|---|---|
| 1 | `Dashboard` | semua | — | Stats live dari DB |
| 2 | `ManageUser` | admin | — | CRUD user + role |
| 3 | `ManageStudent` | admin | — | CRUD murid + DB transaction |
| 4 | `ManageSubject` | admin | — | Search + pagination |
| 5 | `CreateSubject` | admin | — | Validasi unique subject_code |
| 6 | `ManageClass` | admin | — | CRUD + kenaikan kelas otomatis (DB transaction) |
| 7 | `ManageSchedule` | admin | — | CRUD jadwal dengan validasi waktu |
| 8 | `ManageSettings` | admin | — | Key-value settings sekolah |
| 9 | `TeacherAttendance` | guru | `schedules()` | Input absensi per pertemuan |
| 10 | `AttendanceRecap` | guru, admin | `kelasList()`, `rekapData()` | Rekap % kehadiran |
| 11 | `ManageGrade` | guru | `schedules()` | Input nilai UTS/UAS + validasi 0-100 |
| 12 | `AdminGradeRecap` | guru, admin | `kelasList()`, `rekapData()` | Rekap nilai per kelas |
| 13 | `Attendance` | murid | — | View absensi murid sendiri |
| 14 | `StudentGrade` | murid | `gradeRecords()`, `summary()` | View nilai murid sendiri |
| 15 | `AcademicCalendar` | semua (edit: admin) | `calendarWeeks()`, `upcomingEvents()`, `monthLabel()` | Kalender + CRUD event |

### Daftar Route (Web)
| Route | Komponen | Akses |
|---|---|---|
| `GET /` | redirect login | publik |
| `GET /login` | Auth\Login | publik |
| `GET /health` | closure JSON | publik |
| `GET /dashboard` | Dashboard | auth |
| `GET /attendance` | Attendance | auth (murid) |
| `GET /attendance-recap` | AttendanceRecap | auth (guru, admin) |
| `GET /teacher-attendance` | TeacherAttendance | auth (guru) |
| `GET /manage-grade` | ManageGrade | auth (guru) |
| `GET /student-grade` | StudentGrade | auth (murid) |
| `GET /admin-grade-recap` | AdminGradeRecap | auth (guru, admin) |
| `GET /manage-user` | ManageUser | auth (admin) |
| `GET /manage-subject` | ManageSubject | auth (admin) |
| `GET /manage-subject/create` | CreateSubject | auth (admin) |
| `GET /manage-class` | ManageClass | auth (admin) |
| `GET /manage-schedule` | ManageSchedule | auth (admin) |
| `GET /manage-student` | ManageStudent | auth (admin) |
| `GET /manage-settings` | ManageSettings | auth (admin) |
| `GET /settings/profile` | Settings\Profile | auth |
| `GET /settings/password` | Settings\Password | auth |
| `GET /settings/appearance` | Settings\Appearance | auth |
| `GET /settings/two-factor` | Settings\TwoFactor | auth |

---

## BAGIAN 2: PEST SETUP & KONVENSI

### A. Sintaks yang Digunakan

```php
// ✅ Gunakan it() untuk behavior description
it('redirects guest to login when accessing dashboard');
it('allows admin to create a new user');
it('validates that nilai cannot exceed 100');

// ✅ Gunakan describe() untuk mengelompokkan skenario dalam satu komponen
describe('ManageGrade', function () {
    it('loads student list when schedule is selected');
    it('shows error when nilai is above 100');
});

// ✅ Gunakan test() untuk kasus yang perlu deskripsi panjang
test('kenaikan kelas otomatis memindahkan murid level 6 ke status lulus');

// ❌ HINDARI class-based test (kecuali Dusk)
// class ManageUserTest extends TestCase { ... }  // JANGAN
```

### B. Pest Plugin yang Dibutuhkan

| Plugin | Status | Kegunaan di Project |
|---|---|---|
| `pestphp/pest` ^4.1 | ✅ Sudah ada | Core |
| `pestphp/pest-plugin-laravel` ^4.0 | ✅ Sudah ada | `actingAs()`, `get()`, `post()` |
| `pestphp/pest-plugin-livewire` | ❌ Belum ada | `Livewire::test()` dalam sintaks Pest |
| `pestphp/pest-plugin-faker` | ❌ Belum ada | Data generator (FakerPHP sudah ada via Laravel) |
| `pestphp/pest-plugin-watch` | ❌ Opsional | Auto-rerun saat file berubah |
| `pestphp/pest-plugin-parallel` | ❌ Opsional | Jalankan test paralel di CI |
| `laravel/dusk` | ❌ Belum ada | E2E browser test (class-based, pengecualian) |

### C. Dataset & Data Provider

Gunakan `->with()` untuk parameterized test, terutama pada validasi:

```php
// Contoh pola yang akan dipakai di project ini
it('validates required fields on user creation')
    ->with([
        ['form_name', ''],
        ['form_email', ''],
        ['form_role', ''],
        ['form_password', ''],
    ]);

// Untuk validasi nilai 0-100
it('rejects invalid nilai range')
    ->with([[-1], [101], [200], [-999]]);

// Untuk role authorization
it('blocks access for unauthorized roles')
    ->with([
        ['murid', '/manage-user', 403],
        ['guru',  '/manage-user', 403],
        ['murid', '/manage-class', 403],
    ]);
```

### D. Shared Setup dengan beforeEach / afterEach

```php
// Pattern untuk test Livewire yang butuh user tertentu
describe('ManageUser', function () {
    beforeEach(function () {
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($this->admin);
    });

    it('shows user list');
    it('can create new user');
});

// Pattern untuk test yang butuh data seed
beforeEach(function () {
    $this->kelas = Kelas::factory()->create();
    $this->subject = Subject::factory()->create();
});
```

### E. Custom Helpers & Expectations

Rencana custom `expect()` yang akan dibuat di `tests/Pest.php`:

| Nama Expectation | Contoh Penggunaan | Logic |
|---|---|---|
| `toBeAdmin()` | `expect($user)->toBeAdmin()` | `$user->role === 'admin'` |
| `toBeGuru()` | `expect($user)->toBeGuru()` | `$user->role === 'guru'` |
| `toBeMurid()` | `expect($user)->toBeMurid()` | `$user->role === 'murid'` |
| `toBeAktif()` | `expect($murid)->toBeAktif()` | `$murid->status === 'aktif'` |
| `toBeLulus()` | `expect($murid)->toBeLulus()` | `$murid->status === 'lulus'` |
| `toHaveValidHexColor()` | `expect($color)->toHaveValidHexColor()` | `preg_match('/^#[0-9A-Fa-f]{6}$/', $color)` |
| `toBeValidNilai()` | `expect($nilai)->toBeValidNilai()` | `$nilai >= 0 && $nilai <= 100` |

---

## BAGIAN 3: TESTING PYRAMID & PROPORSI

```
         /\
        /E2E\        5%  — Dusk: critical user journey, Alpine.js behavior
       /------\
      / Feature\    20%  — HTTP + Livewire + DB
     /----------\
    /Integration \  15%  — Service layer + DB side effects (event, queue, cache)
   /--------------\
  /   Unit Tests   \ 60% — Model methods, scopes, helpers, business logic terisolasi
 /------------------\
```

| Layer | % | Fokus di Project Ini |
|---|---|---|
| **Unit** | 60% | Model scopes (`scopeUpcoming`), static methods (`defaultColor`, `get`, `set`), `initials()`, `currentTahunAjaran()`, computed logic terisolasi |
| **Integration** | 15% | `processPromotion()` dengan DB transaction, `rekapData()` query, cascade delete, pivot table operations |
| **Feature** | 20% | Auth redirect, role-based access, Livewire CRUD flows, validasi form, grade submission |
| **E2E (Dusk)** | 5% | Login flow, input absensi guru, input nilai, kalender akademik, kenaikan kelas modal |

---

## BAGIAN 4: UNIT TEST PLAN

> Semua unit test menggunakan `factory()->make()` (tanpa DB) kecuali memang perlu DB.

### A. Model Unit Tests

#### User Model

| Model | `it()` description | Skenario | Expected Result | Pest Assertion | Prioritas |
|---|---|---|---|---|---|
| User | `it('generates correct initials from single word name')` | name = 'Budi' | 'B' | `toBe('B')` | 🟡 High |
| User | `it('generates correct initials from two word name')` | name = 'Budi Santoso' | 'BS' | `toBe('BS')` | 🔴 Critical |
| User | `it('generates correct initials from three word name')` | name = 'Budi Eko Santoso' | 'BE' | `toBe('BE')` | 🟡 High |
| User | `it('has admin role')` | role = 'admin' | `toBeAdmin()` | custom expectation | 🟢 Medium |
| User | `it('has guru role')` | role = 'guru' | `toBeGuru()` | custom expectation | 🟢 Medium |
| User | `it('has murid role')` | role = 'murid' | `toBeMurid()` | custom expectation | 🟢 Medium |
| User | `it('hides password from serialization')` | make user | password tidak ada di array | `not->toHaveKey('password')` | 🟡 High |

#### AcademicEvent Model

| Model | `it()` description | Skenario | Expected Result | Pest Assertion | Prioritas |
|---|---|---|---|---|---|
| AcademicEvent | `it('returns red color for libur category')` | `defaultColor('libur')` | '#EF4444' | `toBe('#EF4444')` | 🔴 Critical |
| AcademicEvent | `it('returns orange color for ujian category')` | `defaultColor('ujian')` | '#F28B2B' | `toBe('#F28B2B')` | 🔴 Critical |
| AcademicEvent | `it('returns green color for kegiatan category')` | `defaultColor('kegiatan')` | '#10B981' | `toBe('#10B981')` | 🔴 Critical |
| AcademicEvent | `it('returns blue as default color for umum category')` | `defaultColor('umum')` | '#0F609B' | `toBe('#0F609B')` | 🔴 Critical |
| AcademicEvent | `it('returns default color for unknown category')` | `defaultColor('invalid')` | '#0F609B' | `toBe('#0F609B')` | 🟡 High |
| AcademicEvent | `it('casts start_date as Carbon date')` | make event | instance of Carbon | `toBeInstanceOf(Carbon::class)` | 🟡 High |
| AcademicEvent | `it('casts end_date as Carbon date when present')` | make event dengan end_date | instance of Carbon | `toBeInstanceOf(Carbon::class)` | 🟡 High |
| AcademicEvent | `it('allows null end_date')` | make event tanpa end_date | null | `toBeNull()` | 🟢 Medium |
| AcademicEvent | `it('color value matches valid hex format')` | default color output | matches `/^#[0-9A-Fa-f]{6}$/` | `toHaveValidHexColor()` | 🟡 High |

#### SchoolSetting Model

| Model | `it()` description | Skenario | Expected Result | Pest Assertion | Prioritas |
|---|---|---|---|---|---|
| SchoolSetting | `it('returns default value when key does not exist')` | `get('nonexistent', 'fallback')` | 'fallback' | `toBe('fallback')` | 🔴 Critical |
| SchoolSetting | `it('returns empty string as default when no default given')` | `get('nonexistent')` | '' | `toBe('')` | 🟡 High |

#### Nilai Model

| Model | `it()` description | Skenario | Expected Result | Pest Assertion | Prioritas |
|---|---|---|---|---|---|
| Nilai | `it('casts nilai as integer')` | make nilai dengan nilai = '85' | 85 (integer) | `toBe(85)` | 🔴 Critical |
| Nilai | `it('casts semester as integer')` | make nilai dengan semester = '1' | 1 (integer) | `toBe(1)` | 🟡 High |

#### ProfilMurid Model

| Model | `it()` description | Skenario | Expected Result | Pest Assertion | Prioritas |
|---|---|---|---|---|---|
| ProfilMurid | `it('has aktif status by default')` | make murid tanpa status | 'aktif' | `toBe('aktif')` | 🟡 High |
| ProfilMurid | `it('can have lulus status')` | make murid status lulus | `toBeLulus()` | custom expectation | 🟢 Medium |

#### Subject Model

| Model | `it()` description | Skenario | Expected Result | Pest Assertion | Prioritas |
|---|---|---|---|---|---|
| Subject | `it('casts is_active as boolean')` | make subject is_active = 1 | true (bool) | `toBeTrue()` | 🟡 High |
| Subject | `it('is active by default')` | make subject tanpa is_active | true | `toBeTrue()` | 🟢 Medium |

### B. Service Unit Tests

> Tidak ada Service class di project ini. Section ini tidak berlaku.

### C. Helper / Utility Unit Tests

| Helper/Method | `it()` description | Skenario | Pest Assertion | Prioritas |
|---|---|---|---|---|
| `ManageGrade::currentTahunAjaran()` | `it('returns current/next year when month is July or later')` | bulan >= 7 | format 'YYYY/YYYY+1' | `toMatch('/^\d{4}\/\d{4}$/')` | 🔴 Critical |
| `ManageGrade::currentTahunAjaran()` | `it('returns prev/current year when month is before July')` | bulan < 7 | format 'YYYY-1/YYYY' | `toMatch('/^\d{4}\/\d{4}$/')` | 🔴 Critical |
| `AcademicCalendar::monthName()` | `it('returns correct Indonesian month name for January')` | 1 | 'Januari' | `toBe('Januari')` | 🟢 Medium |
| `AcademicCalendar::monthName()` | `it('returns correct Indonesian month name for December')` | 12 | 'Desember' | `toBe('Desember')` | 🟢 Medium |

### D. Pest Expectations yang Dipakai di Unit Test

```php
expect($value)->toBe($expected)           // strict equality
expect($value)->toEqual($expected)        // loose equality (array, object)
expect($value)->toBeTrue()
expect($value)->toBeFalse()
expect($value)->toBeNull()
expect($value)->not->toBeNull()
expect($value)->toBeInstanceOf(Carbon::class)
expect($value)->toContain('string')
expect($value)->toHaveKey('key')
expect($value)->not->toHaveKey('password')
expect($value)->toMatch('/^#[0-9A-Fa-f]{6}$/')
expect($fn)->toThrow(Exception::class)
expect($collection)->toHaveCount(3)
expect($int)->toBeGreaterThanOrEqual(0)
expect($int)->toBeLessThanOrEqual(100)
```

---

## BAGIAN 5: INTEGRATION TEST PLAN

> Semua integration test menggunakan `uses(RefreshDatabase::class)` dan menyentuh DB nyata (SQLite in-memory).

| `it()` description | Layer yang Terlibat | Skenario | Side Effect yang Dicek | Pest Assertion | Prioritas |
|---|---|---|---|---|---|
| `it('saves upcoming event to database')` | AcademicEvent + DB | create event | record ada di tabel | `expect(AcademicEvent::count())->toBe(1)` | 🔴 Critical |
| `it('scopeUpcoming returns only future events')` | AcademicEvent + DB | 3 event: past, today, future | hanya 2 yang muncul | `expect($events)->toHaveCount(2)` | 🔴 Critical |
| `it('scopeUpcoming includes ongoing multi-day events')` | AcademicEvent + DB | event mulai kemarin, selesai besok | termasuk dalam upcoming | `expect($events)->toHaveCount(1)` | 🔴 Critical |
| `it('saves nilai with unique constraint')` | Nilai + DB | simpan nilai duplikat | exception duplicate key | `toThrow(\Illuminate\Database\QueryException::class)` | 🔴 Critical |
| `it('updates existing nilai on duplicate key scenario')` | Nilai + DB | upsert nilai yang sama | count tetap 1, nilai terupdate | `expect(Nilai::count())->toBe(1)` | 🔴 Critical |
| `it('processPromotion promotes level 5 student to level 6')` | ManageClass + Kelas + ProfilMurid + DB | proses kenaikan kelas | murid punya kelas level 6 di tahun baru | `expect($student->kelas()->...)->not->toBeNull()` | 🔴 Critical |
| `it('processPromotion sets status lulus for level 6 student')` | ManageClass + ProfilMurid + DB | murid level 6 ikut proses | status murid menjadi 'lulus' | `expect($student->fresh()->status)->toBe('lulus')` | 🔴 Critical |
| `it('processPromotion keeps stay-back student in same level')` | ManageClass + DB | murid ditandai tinggal kelas | tetap di level yang sama di tahun baru | `expect($nextClass->level)->toBe($currentClass->level)` | 🔴 Critical |
| `it('processPromotion wraps in transaction and rolls back on failure')` | ManageClass + DB | simulasi error di tengah proses | tidak ada data yang tersimpan | `expect(Kelas::count())->toBe($countBefore)` | 🔴 Critical |
| `it('SchoolSetting get returns stored value from database')` | SchoolSetting + DB | seed data, panggil get() | nilai yang disimpan | `toBe('nilai_tersimpan')` | 🟡 High |
| `it('SchoolSetting set updates value in database')` | SchoolSetting + DB | panggil set() lalu get() | nilai baru | `toBe('nilai_baru')` | 🟡 High |
| `it('deleting kelas does not orphan related pivot records')` | Kelas + kelas_murid + DB | hapus kelas | pivot record ikut terhapus | `expect(DB::table('kelas_murid')->count())->toBe(0)` | 🟡 High |
| `it('absensi record ties correctly to pertemuan_kelas')` | Absensi + PertemuanKelas + DB | buat absensi | relasi pertemuanKelas ada | `expect($absensi->pertemuanKelas)->not->toBeNull()` | 🟡 High |
| `it('rekapData computes correct attendance percentage')` | AttendanceRecap + DB | 10 absensi, 8 Hadir | 80% | `expect($rekap['persentase'])->toBe(80.0)` | 🔴 Critical |
| `it('ManageStudent save uses DB transaction when creating user and profil')` | ManageStudent + User + ProfilMurid + DB | simulasi error setelah User::create | User tidak tersimpan jika ProfilMurid gagal | `expect(User::count())->toBe(0)` | 🟡 High |

---

## BAGIAN 6: FEATURE TEST PLAN

### A. Authentication & Authorization Tests

| Route | Method | Aktor | `it()` description | Expected HTTP Status | Pest Assertion | Prioritas |
|---|---|---|---|---|---|---|
| `/dashboard` | GET | Guest | `it('redirects guest to login when accessing dashboard')` | 302 | `assertRedirect('/login')` | 🔴 Critical |
| `/manage-user` | GET | Guest | `it('redirects guest to login when accessing manage-user')` | 302 | `assertRedirect('/login')` | 🔴 Critical |
| `/manage-user` | GET | Murid | `it('blocks murid from accessing manage-user')` | 302/403 | `assertForbidden()` atau redirect | 🔴 Critical |
| `/manage-user` | GET | Guru | `it('blocks guru from accessing manage-user')` | 302/403 | `assertForbidden()` | 🔴 Critical |
| `/manage-user` | GET | Admin | `it('allows admin to access manage-user')` | 200 | `assertOk()` | 🔴 Critical |
| `/manage-class` | GET | Murid | `it('blocks murid from accessing manage-class')` | 302/403 | `assertForbidden()` | 🔴 Critical |
| `/manage-class` | GET | Admin | `it('allows admin to access manage-class')` | 200 | `assertOk()` | 🔴 Critical |
| `/manage-settings` | GET | Guru | `it('blocks guru from accessing manage-settings')` | 302/403 | `assertForbidden()` | 🔴 Critical |
| `/manage-settings` | GET | Admin | `it('allows admin to access manage-settings')` | 200 | `assertOk()` | 🔴 Critical |
| `/teacher-attendance` | GET | Murid | `it('blocks murid from accessing teacher-attendance')` | 302/403 | `assertForbidden()` | 🔴 Critical |
| `/teacher-attendance` | GET | Guru | `it('allows guru to access teacher-attendance')` | 200 | `assertOk()` | 🔴 Critical |
| `/attendance-recap` | GET | Murid | `it('blocks murid from accessing attendance-recap')` | 302/403 | `assertForbidden()` | 🔴 Critical |
| `/manage-grade` | GET | Murid | `it('blocks murid from accessing manage-grade')` | 302/403 | `assertForbidden()` | 🔴 Critical |
| `/manage-grade` | GET | Guru | `it('allows guru to access manage-grade')` | 200 | `assertOk()` | 🔴 Critical |
| `/student-grade` | GET | Guru | `it('blocks guru from accessing student-grade')` | 302/403 | `assertForbidden()` | 🟡 High |
| `/student-grade` | GET | Murid | `it('allows murid to access student-grade')` | 200 | `assertOk()` | 🔴 Critical |
| `/admin-grade-recap` | GET | Murid | `it('blocks murid from accessing admin-grade-recap')` | 302/403 | `assertForbidden()` | 🔴 Critical |
| `/dashboard` | GET | Admin | `it('allows admin to access dashboard')` | 200 | `assertOk()` | 🔴 Critical |
| `/login` | GET | Guest | `it('renders login page for guest')` | 200 | `assertOk()` | 🟡 High |
| `/login` | GET | Auth | `it('redirects authenticated user away from login')` | 302 | `assertRedirect()` | 🟡 High |

### B. CRUD Feature Tests

#### ManageUser

| Resource | Operasi | `it()` description | Validasi yang Dicek | Pest Assertion | Prioritas |
|---|---|---|---|---|---|
| User | Create | `it('admin can create new user with valid data')` | semua field valid | `assertDatabaseHas('users', ...)` | 🔴 Critical |
| User | Create | `it('validates name is required when creating user')` | name kosong | `assertHasErrors('form_name')` | 🔴 Critical |
| User | Create | `it('validates email format when creating user')` | email invalid | `assertHasErrors('form_email')` | 🔴 Critical |
| User | Create | `it('validates email must be unique when creating user')` | email duplikat | `assertHasErrors('form_email')` | 🔴 Critical |
| User | Create | `it('validates password minimum 8 characters when creating user')` | password < 8 char | `assertHasErrors('form_password')` | 🔴 Critical |
| User | Create | `it('validates role must be valid enum value')` | role = 'superadmin' | `assertHasErrors('form_role')` | 🟡 High |
| User | Update | `it('admin can update user without changing password')` | password kosong saat edit | password tidak berubah di DB | 🔴 Critical |
| User | Update | `it('allows email unchanged when editing same user')` | email sama dengan existing | tidak error unique | 🔴 Critical |
| User | Delete | `it('admin can delete a user')` | hapus user lain | `assertDatabaseMissing('users', ...)` | 🔴 Critical |
| User | Delete | `it('prevents admin from deleting their own account')` | hapus diri sendiri | flash error muncul, user masih ada | 🔴 Critical |

#### ManageClass & Kenaikan Kelas

| Resource | Operasi | `it()` description | Validasi yang Dicek | Pest Assertion | Prioritas |
|---|---|---|---|---|---|
| Kelas | Create | `it('admin can create a new class')` | data valid | `assertDatabaseHas('kelas', ...)` | 🔴 Critical |
| Kelas | Create | `it('validates kode_kelas is unique')` | kode duplikat | `assertHasErrors('kode_kelas')` | 🔴 Critical |
| Kelas | Update | `it('admin can edit existing class')` | update nama_kelas | DB terupdate | 🟡 High |
| Kelas | Delete | `it('admin can delete a class')` | hapus kelas | `assertDatabaseMissing('kelas', ...)` | 🟡 High |
| Promotion | Step 1→2 | `it('promotion step advances from 1 to 2 after entering academic year')` | klik lanjut | `promotionStep` = 2 | 🔴 Critical |
| Promotion | Process | `it('promotion creates new class for next academic year')` | proses kenaikan | kelas baru ada di DB | 🔴 Critical |
| Promotion | Process | `it('promotion marks level 6 student as lulus')` | murid level 6 | status = 'lulus' | 🔴 Critical |

#### ManageStudent

| Resource | Operasi | `it()` description | Validasi yang Dicek | Pest Assertion | Prioritas |
|---|---|---|---|---|---|
| Murid | Create | `it('admin can register new student with user account')` | data valid | user + profil_murid ada di DB | 🔴 Critical |
| Murid | Create | `it('validates NIS must be unique')` | NIS duplikat | `assertHasErrors('nis')` | 🔴 Critical |
| Murid | Create | `it('validates email must be unique for student')` | email duplikat | `assertHasErrors('email')` | 🔴 Critical |
| Murid | Update | `it('admin can update student without changing password')` | password kosong | password tidak berubah | 🔴 Critical |
| Murid | Status | `it('admin can change student status to lulus')` | update status | `assertDatabaseHas('profil_murid', ['status' => 'lulus'])` | 🟡 High |

#### AcademicCalendar (Event CRUD)

| Resource | Operasi | `it()` description | Validasi yang Dicek | Pest Assertion | Prioritas |
|---|---|---|---|---|---|
| Event | Create | `it('admin can create academic event')` | data valid | `assertDatabaseHas('academic_events', ...)` | 🔴 Critical |
| Event | Create | `it('validates event title is required')` | title kosong | `assertHasErrors('form_title')` | 🟡 High |
| Event | Create | `it('validates end_date must be after start_date')` | end < start | `assertHasErrors('form_end_date')` | 🟡 High |
| Event | Create | `it('validates color must be valid hex format')` | color = 'red' | `assertHasErrors('form_color')` | 🟡 High |
| Event | Delete | `it('admin can delete academic event')` | hapus event | `assertDatabaseMissing('academic_events', ...)` | 🟡 High |
| Event | Auth | `it('non-admin cannot create academic event')` | murid/guru coba CRUD | `abort(403)` | 🔴 Critical |
| Event | Update | `it('updating category auto-updates color')` | ubah category | color berubah sesuai defaultColor() | 🟡 High |

#### ManageSettings

| Resource | Operasi | `it()` description | Validasi yang Dicek | Pest Assertion | Prioritas |
|---|---|---|---|---|---|
| Settings | Update | `it('admin can update jumlah_ekskul')` | simpan nilai baru | DB terupdate | 🟡 High |
| Settings | Update | `it('admin can update tingkat_akreditasi')` | simpan nilai baru | DB terupdate | 🟡 High |
| Settings | Validate | `it('validates jumlah_ekskul must be integer')` | input 'abc' | `assertHasErrors('jumlah_ekskul')` | 🟡 High |
| Settings | Validate | `it('validates tingkat_akreditasi max 10 characters')` | 11+ karakter | `assertHasErrors('tingkat_akreditasi')` | 🟢 Medium |

#### ManageGrade (Nilai)

| Resource | Operasi | `it()` description | Validasi yang Dicek | Pest Assertion | Prioritas |
|---|---|---|---|---|---|
| Nilai | Submit | `it('guru can submit valid grades for students')` | nilai 0-100 | `assertDatabaseHas('nilais', ...)` | 🔴 Critical |
| Nilai | Validate | `it('rejects nilai above 100')` | nilai = 101 | `assertHasErrors(...)` | 🔴 Critical |
| Nilai | Validate | `it('rejects nilai below 0')` | nilai = -1 | `assertHasErrors(...)` | 🔴 Critical |
| Nilai | Auth | `it('guru cannot input grades for class they do not teach')` | schedule orang lain | `abort(403)` | 🔴 Critical |
| Nilai | Submit | `it('skips saving when nilai field is empty')` | nilai kosong | tidak ada record di DB | 🟡 High |
| Nilai | Upsert | `it('updates existing nilai on re-submission')` | submit nilai 2x | tetap 1 record, nilai terupdate | 🔴 Critical |

### C. Livewire Component Tests

| Komponen | `it()` description | Action yang Ditest | Pest Assertion | Prioritas |
|---|---|---|---|---|
| `ManageUser` | `it('shows user list on mount')` | mount | `assertSee($user->name)` | 🔴 Critical |
| `ManageUser` | `it('filters users by search term')` | set search | hanya user relevan tampil | 🔴 Critical |
| `ManageUser` | `it('filters users by role')` | set selectedRole | hanya role tersebut tampil | 🟡 High |
| `ManageUser` | `it('opens add modal when add button clicked')` | `openAddForm()` | `assertSet('showModal', true)` | 🔴 Critical |
| `ManageUser` | `it('prefills form when edit button clicked')` | `openEditForm($id)` | `assertSet('form_name', ...)` | 🔴 Critical |
| `ManageUser` | `it('closes modal and resets form on closeModal')` | `closeModal()` | `assertSet('showModal', false)` | 🟡 High |
| `ManageClass` | `it('shows class list on mount')` | mount | `assertSee($kelas->nama_kelas)` | 🔴 Critical |
| `ManageClass` | `it('opens promotion modal on openPromotion')` | `openPromotion()` | `assertSet('showPromotionModal', true)` | 🔴 Critical |
| `ManageClass` | `it('advances promotion to step 2 on startPromotion')` | `startPromotion()` | `assertSet('promotionStep', 2)` | 🔴 Critical |
| `TeacherAttendance` | `it('loads student list when schedule and date are set')` | `loadStudents()` | `assertSet('students', ...)` | 🔴 Critical |
| `TeacherAttendance` | `it('only loads students from teacher own schedule')` | schedule guru lain | abort 403 | 🔴 Critical |
| `TeacherAttendance` | `it('saves attendance records on submit')` | `submit()` | `assertDatabaseHas('absensi', ...)` | 🔴 Critical |
| `AttendanceRecap` | `it('blocks murid from accessing attendance recap')` | mount sebagai murid | abort 403 | 🔴 Critical |
| `AttendanceRecap` | `it('shows recap data filtered by bulan and tahun')` | set bulan/tahun | data sesuai filter | 🔴 Critical |
| `ManageGrade` | `it('adds error when nilai exceeds 100 on input')` | `updatedGrades(101, 'id.uts')` | `assertHasErrors(...)` | 🔴 Critical |
| `ManageGrade` | `it('adds error when nilai is negative')` | `updatedGrades(-1, 'id.uts')` | `assertHasErrors(...)` | 🔴 Critical |
| `ManageGrade` | `it('clears error when nilai is corrected to valid range')` | `updatedGrades(85, 'id.uts')` | `assertHasNoErrors(...)` | 🟡 High |
| `StudentGrade` | `it('shows only grades belonging to logged-in student')` | mount sebagai murid | grade murid lain tidak tampil | 🔴 Critical |
| `AcademicCalendar` | `it('renders calendar for current month on mount')` | mount | `assertSet('month', now()->month)` | 🟡 High |
| `AcademicCalendar` | `it('navigates to previous month on previousMonth call')` | `previousMonth()` | month berkurang 1 | 🟡 High |
| `AcademicCalendar` | `it('navigates to next month on nextMonth call')` | `nextMonth()` | month bertambah 1 | 🟡 High |
| `AcademicCalendar` | `it('wraps month to December when going back from January')` | month=1, `previousMonth()` | month=12, year-1 | 🟡 High |
| `AcademicCalendar` | `it('wraps month to January when going forward from December')` | month=12, `nextMonth()` | month=1, year+1 | 🟡 High |
| `AcademicCalendar` | `it('opens add form modal for admin')` | `openAddForm()` | `assertSet('showFormModal', true)` | 🔴 Critical |
| `AcademicCalendar` | `it('aborts 403 when non-admin tries to add event')` | `openAddForm()` sebagai guru | 403 | 🔴 Critical |
| `AcademicCalendar` | `it('saves new academic event to database')` | `saveEvent()` | `assertDatabaseHas('academic_events', ...)` | 🔴 Critical |
| `AcademicCalendar` | `it('deletes academic event from database')` | `deleteEvent($id)` | `assertDatabaseMissing(...)` | 🔴 Critical |
| `ManageSettings` | `it('blocks non-admin from accessing settings')` | mount sebagai guru | abort 403 | 🔴 Critical |
| `ManageSettings` | `it('loads current settings on mount')` | mount | `assertSet('jumlah_ekskul', ...)` | 🟡 High |
| `ManageSettings` | `it('saves updated settings to database')` | `save()` | DB terupdate | 🟡 High |
| `Dashboard` | `it('shows correct murid count from database')` | mount dengan 5 murid | stat value = '5' | 🟡 High |
| `Dashboard` | `it('shows correct upcoming event count')` | mount dengan 2 event mendatang | stat value = '2' | 🟡 High |

### D. Security Feature Tests

#### Mass Assignment Tests

| Model | Field Berbahaya | `it()` description | Assertion | Prioritas |
|---|---|---|---|---|
| User | `role` via HTTP | `it('cannot mass-assign role through user creation endpoint')` | role tetap default | 🔴 Critical |
| ProfilMurid | `status` via HTTP | `it('cannot mass-assign status lulus through registration')` | status tetap 'aktif' | 🔴 Critical |
| Nilai | `guru_id` via HTTP | `it('cannot mass-assign guru_id different from authenticated guru')` | guru_id = auth user | 🔴 Critical |

#### SQL Injection Tests

| Endpoint / Komponen | Parameter | `it()` description | Assertion | Prioritas |
|---|---|---|---|---|
| ManageUser search | `$search` | `it('search input is safe from SQL injection')` | tidak error, hasil kosong/normal | 🔴 Critical |
| ManageClass search | `$search` | `it('class search is safe from SQL injection')` | tidak error | 🔴 Critical |
| ManageStudent search | `$search` | `it('student search is safe from SQL injection')` | tidak error | 🔴 Critical |

Payload yang dicoba via `->with([...])`:
```php
->with([
    ["' OR '1'='1"],
    ["'; DROP TABLE users; --"],
    ["1 UNION SELECT * FROM users"],
])
```

#### XSS Tests

| Halaman | Field | `it()` description | Assertion | Prioritas |
|---|---|---|---|---|
| ManageUser | `form_name` | `it('escapes XSS payload in user name field')` | payload tidak dirender sebagai HTML | 🔴 Critical |
| AcademicCalendar | `form_title` | `it('escapes XSS payload in event title')` | payload ter-escape | 🔴 Critical |
| ManageStudent | `nama_lengkap` | `it('escapes XSS payload in student name')` | payload ter-escape | 🔴 Critical |

#### CSRF Tests

| Form / Endpoint | `it()` description | Expected Status | Prioritas |
|---|---|---|---|
| Semua Livewire form | `it('Livewire requests include CSRF token automatically')` | 200 (built-in Livewire) | 🟡 High |
| `/login` POST | `it('login form rejects request without CSRF token')` | 419 | 🔴 Critical |

#### Rate Limiting Tests

| Endpoint | Limit Threshold | `it()` description | Assertion | Prioritas |
|---|---|---|---|---|
| `/login` | 5 percobaan/menit | `it('rate limits login attempts after threshold')` | HTTP 429 setelah limit | 🔴 Critical |

### E. Performance Feature Tests

| `it()` description | Endpoint / Komponen | Metric | Threshold | Cara Mengukur | Prioritas |
|---|---|---|---|---|---|
| `it('dashboard loads with fewer than 10 queries')` | Dashboard | Query count | < 10 | `DB::enableQueryLog()` | 🟡 High |
| `it('attendance recap loads with fewer than 15 queries')` | AttendanceRecap | Query count | < 15 | `DB::enableQueryLog()` | 🟡 High |
| `it('calendar weeks computation is efficient with 30 events')` | AcademicCalendar `calendarWeeks()` | Query count | = 1 | `DB::enableQueryLog()` | 🟡 High |
| `it('manage user list loads within acceptable time')` | ManageUser | Response time | < 500ms | `microtime()` diff | 🟢 Medium |
| `it('grade recap does not use N+1 queries')` | AdminGradeRecap | Query count | < 5 | `DB::enableQueryLog()` | 🔴 Critical |

---

## BAGIAN 7: E2E TEST PLAN (Laravel Dusk)

> **⚠️ PENGECUALIAN KHUSUS**: Dusk test tetap menggunakan class-based karena `pest-plugin-dusk` yang stabil untuk Pest v4 belum tersedia. Ini adalah satu-satunya layer yang tidak menggunakan sintaks Pest. Semua file di `tests/Browser/` menggunakan `extends DuskTestCase`.

### A. Critical User Journey

| Journey Name | Steps | Alpine.js Interaction | Livewire Interaction | Dusk Selector Dibutuhkan | Prioritas |
|---|---|---|---|---|---|
| Login Flow | 1. Buka /login → 2. Isi email & password → 3. Klik login → 4. Lihat dashboard | — | wire:model, wire:submit | `@dusk="email-input"`, `@dusk="password-input"`, `@dusk="login-btn"` | 🔴 Critical |
| Input Absensi Guru | 1. Login sebagai guru → 2. Buka /teacher-attendance → 3. Pilih jadwal → 4. Pilih tanggal → 5. Klik muat siswa → 6. Set status absensi → 7. Submit | — | wire:click, wire:submit | `@dusk="schedule-select"`, `@dusk="date-input"`, `@dusk="load-students-btn"`, `@dusk="attendance-row-{id}"` | 🔴 Critical |
| Input Nilai Guru | 1. Login sebagai guru → 2. Buka /manage-grade → 3. Pilih jadwal → 4. Isi nilai UTS/UAS → 5. Submit | Alpine validate border | wire:model, wire:submit | `@dusk="grade-input-uts-{id}"`, `@dusk="grade-input-uas-{id}"`, `@dusk="submit-grade-btn"` | 🔴 Critical |
| Kenaikan Kelas | 1. Login admin → 2. Buka /manage-class → 3. Klik Kenaikan Kelas → 4. Isi tahun ajaran → 5. Klik Lanjut → 6. Centang murid tinggal kelas → 7. Proses | x-show modal, x-transition | wire:click, wire:submit | `@dusk="promotion-btn"`, `@dusk="new-academic-year"`, `@dusk="next-step-btn"`, `@dusk="stayback-checkbox-{id}"`, `@dusk="process-promotion-btn"` | 🔴 Critical |
| Kalender Akademik Admin | 1. Login admin → 2. Buka /dashboard → 3. Klik Tambah di kalender → 4. Isi form event → 5. Simpan → 6. Event muncul di grid | x-show modal, x-transition | wire:click, wire:submit | `@dusk="calendar-add-btn"`, `@dusk="event-title-input"`, `@dusk="event-save-btn"` | 🟡 High |

### B. Alpine.js Specific Tests

| Halaman | Komponen Alpine | Directive yang Ditest | Trigger | Expected Visual | Dusk Method | Prioritas |
|---|---|---|---|---|---|---|
| manage-user | Modal form | `x-show`, `x-transition` | Klik "Tambah User" | Modal muncul dengan animasi | `waitFor()`, `assertVisible()` | 🔴 Critical |
| manage-user | Modal form | `@keydown.escape.window` | Tekan Escape | Modal menutup | `keys(WebDriverKeys::ESCAPE)`, `assertMissing()` | 🟡 High |
| manage-class | Promotion modal | `x-show`, step indicator | Klik "Lanjut" | Step 2 tampil, step indicator update | `waitFor()`, `assertSeeIn()` | 🔴 Critical |
| manage-grade | Grade input | Alpine `gradeInput()` | Input nilai > 100 | Border merah muncul | `assertAttribute(..., 'class', ...)` | 🟡 High |
| academic-calendar | Detail modal | `x-show`, `selectDay()` | Klik cell dengan event | Modal detail event muncul | `waitFor()`, `assertVisible()` | 🟡 High |
| academic-calendar | Form modal | `x-show`, `$wire.showFormModal` | Klik Tambah | Form modal muncul | `waitFor()`, `assertVisible()` | 🟡 High |

### C. Livewire Real-time Tests

| Komponen | Real-time Behavior | Trigger | Expected Update | `pause()` Duration | Prioritas |
|---|---|---|---|---|---|
| `ManageUser` | Search debounce | Ketik di search box | Daftar user ter-filter | `pause(500)` (debounce 300ms + buffer) | 🔴 Critical |
| `ManageStudent` | Search debounce | Ketik di search box | Daftar murid ter-filter | `pause(500)` | 🔴 Critical |
| `ManageClass` | Search debounce | Ketik di search box | Daftar kelas ter-filter | `pause(500)` | 🟡 High |
| `ManageGrade` | Load students | Pilih jadwal | Tabel murid muncul | `pause(300)` | 🔴 Critical |
| `TeacherAttendance` | Load students | Pilih jadwal + tanggal | Daftar siswa muncul | `pause(300)` | 🔴 Critical |
| `AcademicCalendar` | Month navigation | Klik prev/next | Grid kalender update | `pause(300)` | 🟡 High |

### D. Dusk Selector Plan

| View File | Element | `@dusk` Attribute | Dipakai di Test |
|---|---|---|---|
| `login.blade.php` | Input email | `@dusk="email-input"` | Login Flow |
| `login.blade.php` | Input password | `@dusk="password-input"` | Login Flow |
| `login.blade.php` | Tombol login | `@dusk="login-btn"` | Login Flow |
| `manage-user.blade.php` | Tombol tambah user | `@dusk="add-user-btn"` | ManageUser CRUD |
| `manage-user.blade.php` | Modal form | `@dusk="user-modal"` | ManageUser Modal |
| `manage-user.blade.php` | Tombol edit per row | `@dusk="edit-user-{id}"` | ManageUser Edit |
| `manage-class.blade.php` | Tombol Kenaikan Kelas | `@dusk="promotion-btn"` | Kenaikan Kelas Journey |
| `manage-class.blade.php` | Input tahun ajaran baru | `@dusk="new-academic-year"` | Kenaikan Kelas Journey |
| `manage-class.blade.php` | Tombol Lanjut | `@dusk="next-step-btn"` | Kenaikan Kelas Journey |
| `manage-class.blade.php` | Checkbox tinggal kelas | `@dusk="stayback-checkbox-{id}"` | Kenaikan Kelas Journey |
| `manage-class.blade.php` | Tombol Proses | `@dusk="process-promotion-btn"` | Kenaikan Kelas Journey |
| `teacher-attendance.blade.php` | Select jadwal | `@dusk="schedule-select"` | Input Absensi Journey |
| `teacher-attendance.blade.php` | Input tanggal | `@dusk="date-input"` | Input Absensi Journey |
| `teacher-attendance.blade.php` | Tombol muat siswa | `@dusk="load-students-btn"` | Input Absensi Journey |
| `manage-grade.blade.php` | Input UTS per murid | `@dusk="grade-uts-{murid_id}"` | Input Nilai Journey |
| `manage-grade.blade.php` | Input UAS per murid | `@dusk="grade-uas-{murid_id}"` | Input Nilai Journey |
| `manage-grade.blade.php` | Tombol simpan nilai | `@dusk="submit-grade-btn"` | Input Nilai Journey |
| `academic-calendar.blade.php` | Tombol tambah event | `@dusk="calendar-add-btn"` | Kalender Journey |
| `academic-calendar.blade.php` | Form modal event | `@dusk="event-form-modal"` | Kalender Journey |

---

## BAGIAN 8: FOLDER STRUCTURE PLAN

```
tests/
├── Pest.php                          ← global uses(), custom expectations, helper functions
├── TestCase.php                      ← base class (sudah ada)
├── Helpers/
│   └── TestHelpers.php              ← loginAs(), createStudentWithClass(), dll
│
├── Unit/
│   ├── Models/
│   │   ├── UserTest.php             ← 7 it() blocks | uses() di Pest.php | describe() 2 group
│   │   ├── AcademicEventTest.php    ← 9 it() blocks | uses() di Pest.php | describe() 3 group
│   │   ├── SchoolSettingTest.php    ← 2 it() blocks | tidak butuh DB | standalone
│   │   ├── NilaiTest.php            ← 2 it() blocks | uses() di Pest.php
│   │   ├── ProfilMuridTest.php      ← 2 it() blocks | uses() di Pest.php
│   │   └── SubjectTest.php          ← 2 it() blocks | uses() di Pest.php
│   └── Helpers/
│       ├── CalendarHelperTest.php   ← 2 it() blocks (monthName) | tanpa DB
│       └── GradeHelperTest.php      ← 2 it() blocks (currentTahunAjaran) | tanpa DB
│
├── Integration/
│   ├── AcademicEventIntegrationTest.php  ← 3 it() blocks | uses(RefreshDatabase) | describe()
│   ├── NilaiIntegrationTest.php          ← 3 it() blocks | uses(RefreshDatabase) | describe()
│   ├── ClassPromotionTest.php            ← 4 it() blocks | uses(RefreshDatabase) | describe()
│   ├── SchoolSettingIntegrationTest.php  ← 2 it() blocks | uses(RefreshDatabase)
│   ├── AttendanceRekapTest.php           ← 2 it() blocks | uses(RefreshDatabase)
│   └── StudentRegistrationTest.php       ← 2 it() blocks | uses(RefreshDatabase) + describe()
│
├── Feature/
│   ├── Auth/
│   │   ├── AuthenticationTest.php        ← sudah ada, perlu review & extend
│   │   ├── AuthorizationTest.php         ← 20 it() blocks (role-based access) | describe() per role
│   │   └── RateLimitingTest.php          ← 1 it() block
│   │
│   ├── User/
│   │   └── ManageUserTest.php            ← 10 it() blocks | uses(RefreshDatabase) | describe() CRUD
│   │
│   ├── Student/
│   │   └── ManageStudentTest.php         ← 5 it() blocks | uses(RefreshDatabase) | describe()
│   │
│   ├── Class/
│   │   ├── ManageClassTest.php           ← 4 it() blocks | uses(RefreshDatabase)
│   │   └── ClassPromotionFeatureTest.php ← 3 it() blocks | uses(RefreshDatabase) | describe()
│   │
│   ├── Grade/
│   │   ├── ManageGradeTest.php           ← 6 it() blocks | uses(RefreshDatabase) | describe()
│   │   ├── StudentGradeTest.php          ← 2 it() blocks | uses(RefreshDatabase)
│   │   └── AdminGradeRecapTest.php       ← 2 it() blocks | uses(RefreshDatabase)
│   │
│   ├── Attendance/
│   │   ├── TeacherAttendanceTest.php     ← 3 it() blocks | uses(RefreshDatabase) | describe()
│   │   └── AttendanceRecapTest.php       ← 3 it() blocks | uses(RefreshDatabase)
│   │
│   ├── Calendar/
│   │   └── AcademicCalendarTest.php      ← 9 it() blocks | uses(RefreshDatabase) | describe() 3 group
│   │
│   ├── Settings/
│   │   └── ManageSettingsTest.php        ← 4 it() blocks | uses(RefreshDatabase)
│   │
│   ├── Security/
│   │   ├── MassAssignmentTest.php        ← 3 it() blocks
│   │   ├── SqlInjectionTest.php          ← 3 it() blocks dengan ->with() payload
│   │   ├── XssTest.php                   ← 3 it() blocks
│   │   └── CsrfTest.php                  ← 2 it() blocks
│   │
│   └── Performance/
│       └── QueryPerformanceTest.php       ← 5 it() blocks | DB::enableQueryLog()
│
└── Browser/                          ← Dusk (class-based, PENGECUALIAN dari konvensi Pest)
    ├── LoginTest.php                 ← 1 test method
    ├── TeacherAttendanceBrowserTest.php ← 1 test method
    ├── ManageGradeBrowserTest.php    ← 2 test methods
    ├── ClassPromotionBrowserTest.php ← 1 test method
    └── AcademicCalendarBrowserTest.php ← 2 test methods
```

---

## BAGIAN 9: PEST.PHP GLOBAL CONFIGURATION PLAN

### A. Global `uses()` yang Akan Didaftarkan

```php
// tests/Pest.php

// RefreshDatabase untuk semua Feature dan Integration test
uses(RefreshDatabase::class)->in('Feature', 'Integration');

// Unit test TIDAK menggunakan RefreshDatabase secara global
// (hanya Model unit test yang butuh DB yang akan tambah sendiri)

// Tambahkan Livewire testing trait ketika pest-plugin-livewire terinstall
// uses(InteractsWithLivewire::class)->in('Feature');
```

### B. Custom Expectations yang Akan Dibuat

| Nama Expectation | Contoh Penggunaan | Logic di Baliknya |
|---|---|---|
| `toBeAdmin()` | `expect($user)->toBeAdmin()` | `$this->toBe('admin', $value->role)` |
| `toBeGuru()` | `expect($user)->toBeGuru()` | `$this->toBe('guru', $value->role)` |
| `toBeMurid()` | `expect($user)->toBeMurid()` | `$this->toBe('murid', $value->role)` |
| `toBeAktif()` | `expect($murid)->toBeAktif()` | `$this->toBe('aktif', $value->status)` |
| `toBeLulus()` | `expect($murid)->toBeLulus()` | `$this->toBe('lulus', $value->status)` |
| `toHaveValidHexColor()` | `expect('#0F609B')->toHaveValidHexColor()` | `preg_match('/^#[0-9A-Fa-f]{6}$/', $value) === 1` |
| `toBeValidNilai()` | `expect(85)->toBeValidNilai()` | `$value >= 0 && $value <= 100` |

### C. Custom Helper Functions

| Nama Fungsi | Tujuan | Dipakai di Test |
|---|---|---|
| `loginAsAdmin()` | Buat user admin + actingAs sekaligus | Semua Feature test yang butuh admin |
| `loginAsGuru()` | Buat user guru + ProfilGuru + actingAs | TeacherAttendance, ManageGrade |
| `loginAsMurid()` | Buat user murid + ProfilMurid + actingAs | Attendance, StudentGrade |
| `createKelasWithStudents($count)` | Buat kelas + sejumlah murid terpasang | ManageClass, AttendanceRecap, GradeRecap |
| `createAcademicEventFor($date)` | Buat academic event di tanggal tertentu | AcademicCalendar tests |
| `createNilaiForStudent($murid, $subject, $data)` | Buat record nilai lengkap | ManageGrade, StudentGrade |

### D. Dataset yang Akan Di-share Antar Test

```php
// Dataset XSS payload — dipakai di Security tests
dataset('xss_payloads', [
    '<script>alert(1)</script>',
    '<img src=x onerror=alert(1)>',
    'javascript:alert(1)',
]);

// Dataset SQL injection payload
dataset('sql_injection_payloads', [
    "' OR '1'='1",
    "'; DROP TABLE users; --",
    "1 UNION SELECT * FROM users",
]);

// Dataset invalid nilai
dataset('invalid_nilai', [[-1], [101], [200], [-999], [100.5]]);

// Dataset valid nilai boundary
dataset('valid_nilai_boundary', [[0], [1], [99], [100]]);

// Dataset role yang tidak punya akses admin
dataset('non_admin_roles', [['murid'], ['guru']]);
```

---

## BAGIAN 10: PEST CONFIG & RUNNER PLAN

### A. phpunit.xml — Testsuite Definition

Tambahkan testsuite `Integration` ke `phpunit.xml` yang ada:

```xml
<testsuites>
    <testsuite name="Unit">
        <directory>tests/Unit</directory>
    </testsuite>
    <testsuite name="Integration">
        <directory>tests/Integration</directory>
    </testsuite>
    <testsuite name="Feature">
        <directory>tests/Feature</directory>
    </testsuite>
    <testsuite name="Security">
        <directory>tests/Feature/Security</directory>
    </testsuite>
    <testsuite name="Performance">
        <directory>tests/Feature/Performance</directory>
    </testsuite>
</testsuites>
```

### B. Perintah yang Sering Dipakai

```bash
# Jalankan semua test
./vendor/bin/pest

# Jalankan per layer
./vendor/bin/pest --testsuite=Unit
./vendor/bin/pest --testsuite=Integration
./vendor/bin/pest --testsuite=Feature
./vendor/bin/pest --testsuite=Security
./vendor/bin/pest --testsuite=Performance

# Dengan coverage (butuh Xdebug atau PCOV)
./vendor/bin/pest --coverage --min=80

# Filter test tertentu
./vendor/bin/pest --filter="can create user"
./vendor/bin/pest --filter="ManageClass"

# Watch mode (butuh pest-plugin-watch)
./vendor/bin/pest --watch

# Paralel (butuh pest-plugin-parallel)
./vendor/bin/pest --parallel

# Jalankan Dusk terpisah
php artisan dusk
php artisan dusk --filter=LoginTest

# Jalankan hanya test yang gagal terakhir
./vendor/bin/pest --retry
```

### C. `.env.testing` Variables yang Harus Ada

```dotenv
APP_ENV=testing
APP_KEY=base64:...
APP_DEBUG=true

DB_CONNECTION=sqlite
DB_DATABASE=:memory:

# Disable caching
CACHE_DRIVER=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync

# Mail
MAIL_MAILER=array

# Disable Sentry di testing
SENTRY_LARAVEL_DSN=

# Disable Pulse dan Telescope
PULSE_ENABLED=false

# 2FA tetap bisa ditest
FORTIFY_FEATURES=two-factor-authentication
```

---

## BAGIAN 11: DEPENDENCY & SETUP CHECKLIST

### A. Package yang Perlu Diinstall

```bash
# Plugin Pest yang kurang (Core sudah ada)
composer require pestphp/pest-plugin-livewire --dev

# Opsional tapi direkomendasikan
composer require pestphp/pest-plugin-faker --dev
composer require pestphp/pest-plugin-watch --dev
composer require pestphp/pest-plugin-parallel --dev

# E2E
composer require laravel/dusk --dev
php artisan dusk:install
```

### B. Checklist Factory

| Model | Factory Ada? | Field yang Belum Ada di Factory | Prioritas |
|---|---|---|---|
| `User` | ✅ Ada (UserFactory) | `role` field perlu state: admin, guru, murid | 🔴 Critical |
| `ProfilMurid` | ❌ Tidak ada | `user_id`, `nis`, `nama_lengkap`, `status` | 🔴 Critical |
| `ProfilGuru` | ❌ Tidak ada | `user_id`, `nip`, `nama_lengkap` | 🔴 Critical |
| `Kelas` | ❌ Tidak ada | `kode_kelas`, `nama_kelas`, `tahun_ajaran`, `level`, `kelompok`, `wali_kelas_id` | 🔴 Critical |
| `Subject` | ❌ Tidak ada | `subject_code`, `subject_name`, `category`, `is_active` | 🔴 Critical |
| `TeachingSchedule` | ❌ Tidak ada | `guru_id`, `subject_id`, `kelas_id`, `hari`, `jam_mulai`, `jam_selesai` | 🔴 Critical |
| `PertemuanKelas` | ❌ Tidak ada | `teaching_schedule_id`, `tanggal_pertemuan`, `materi` | 🟡 High |
| `Absensi` | ❌ Tidak ada | `pertemuan_kelas_id`, `murid_id`, `status_kehadiran`, `waktu_absen` | 🟡 High |
| `Nilai` | ❌ Tidak ada | `murid_id`, `subject_id`, `guru_id`, `kelas_id`, `tipe_nilai`, `nilai`, `semester`, `tahun_ajaran` | 🔴 Critical |
| `AcademicEvent` | ❌ Tidak ada | `title`, `category`, `start_date`, `end_date`, `color` | 🟡 High |
| `SchoolSetting` | ❌ Tidak ada | `key`, `value`, `label` (bisa pakai seeder saja) | 🟢 Medium |

### C. Checklist Views yang Butuh `@dusk` Attribute

| View File | Element | `@dusk` Attribute | Untuk Test |
|---|---|---|---|
| `auth/login.blade.php` | Input email, password, tombol login | `dusk="email-input"` dll | Login Journey |
| `livewire/manage-user.blade.php` | Tombol tambah, edit, modal | `dusk="add-user-btn"` dll | ManageUser Browser |
| `livewire/manage-class.blade.php` | Tombol kenaikan, step buttons, checkboxes | `dusk="promotion-btn"` dll | Promotion Journey |
| `livewire/teacher-attendance.blade.php` | Select, date, submit | `dusk="schedule-select"` dll | Attendance Journey |
| `livewire/manage-grade.blade.php` | Grade inputs, submit | `dusk="grade-uts-{id}"` dll | Grade Journey |
| `livewire/academic-calendar.blade.php` | Add button, modal, form | `dusk="calendar-add-btn"` dll | Calendar Journey |

### D. Checklist Konfigurasi Tambahan

- [ ] Tambah testsuite `Integration` ke `phpunit.xml`
- [ ] Buat file `.env.testing` dengan konfigurasi SQLite in-memory
- [ ] Pastikan `DB_CONNECTION=sqlite` dan `DB_DATABASE=:memory:` di env testing
- [ ] Jalankan `php artisan dusk:install` setelah install Dusk
- [ ] Setup ChromeDriver untuk Dusk (atau gunakan `php artisan dusk:chrome-driver`)
- [ ] Tambahkan coverage driver (Xdebug atau PCOV) jika ingin `--coverage`

---

## BAGIAN 12: IMPLEMENTASI ROADMAP

### Fase 1 — Foundation (Estimasi: 1-2 hari)
- [ ] Install `pestphp/pest-plugin-livewire`
- [ ] Install `pestphp/pest-plugin-watch` dan `pest-plugin-parallel` (opsional)
- [ ] Install `laravel/dusk` dan jalankan `dusk:install`
- [ ] Buat file `.env.testing`
- [ ] Tambah testsuite `Integration` ke `phpunit.xml`
- [ ] Buat semua Factory yang kurang (10 factory): **UserFactory states, ProfilMurid, ProfilGuru, Kelas, Subject, TeachingSchedule, PertemuanKelas, Absensi, Nilai, AcademicEvent**
- [ ] Tulis `tests/Pest.php` dengan global `uses()` dan semua custom expectations
- [ ] Buat `tests/Helpers/TestHelpers.php` dengan helper functions
- [ ] Tambahkan `@dusk` attribute ke 6 view file yang butuh E2E test

### Fase 2 — Unit Tests (Estimasi: 1-2 hari)
- [ ] 🔴 `AcademicEventTest.php` — `defaultColor()` semua kategori (9 it blocks)
- [ ] 🔴 `UserTest.php` — `initials()` semua skenario (7 it blocks)
- [ ] 🔴 `GradeHelperTest.php` — `currentTahunAjaran()` (2 it blocks)
- [ ] 🟡 `SchoolSettingTest.php` — `get()` dengan default (2 it blocks)
- [ ] 🟡 `NilaiTest.php` — casts integer (2 it blocks)
- [ ] 🟡 `SubjectTest.php` — cast boolean (2 it blocks)
- [ ] 🟢 `ProfilMuridTest.php` — status (2 it blocks)
- [ ] 🟢 `CalendarHelperTest.php` — monthName (2 it blocks)

### Fase 3 — Integration Tests (Estimasi: 2-3 hari)
- [ ] 🔴 `ClassPromotionTest.php` — transaction, lulus, stay-back (4 it blocks)
- [ ] 🔴 `NilaiIntegrationTest.php` — unique constraint, upsert (3 it blocks)
- [ ] 🔴 `AttendanceRekapTest.php` — persentase kalkulasi (2 it blocks)
- [ ] 🟡 `AcademicEventIntegrationTest.php` — scopeUpcoming (3 it blocks)
- [ ] 🟡 `SchoolSettingIntegrationTest.php` — get/set DB (2 it blocks)
- [ ] 🟡 `StudentRegistrationTest.php` — transaction user + profil (2 it blocks)

### Fase 4 — Feature Tests (Estimasi: 3-5 hari)
Urutan yang disarankan:
1. **Auth** → `AuthorizationTest.php` (20 it blocks — semua role × semua route)
2. **User CRUD** → `ManageUserTest.php` (10 it blocks)
3. **Student CRUD** → `ManageStudentTest.php` (5 it blocks)
4. **Class CRUD + Promotion** → `ManageClassTest.php` + `ClassPromotionFeatureTest.php` (7 it blocks)
5. **Grade** → `ManageGradeTest.php` + `StudentGradeTest.php` + `AdminGradeRecapTest.php` (10 it blocks)
6. **Attendance** → `TeacherAttendanceTest.php` + `AttendanceRecapTest.php` (6 it blocks)
7. **Calendar** → `AcademicCalendarTest.php` (9 it blocks)
8. **Settings** → `ManageSettingsTest.php` (4 it blocks)
9. **Security** → `MassAssignmentTest.php`, `SqlInjectionTest.php`, `XssTest.php`, `CsrfTest.php`, `RateLimitingTest.php` (12 it blocks)
10. **Performance** → `QueryPerformanceTest.php` (5 it blocks)

### Fase 5 — E2E Tests / Dusk (Estimasi: 2-3 hari)
- [ ] 🔴 `LoginTest.php` — login flow
- [ ] 🔴 `TeacherAttendanceBrowserTest.php` — input absensi
- [ ] 🔴 `ManageGradeBrowserTest.php` — input nilai + validasi border merah
- [ ] 🔴 `ClassPromotionBrowserTest.php` — modal step 1 → 2 → process
- [ ] 🟡 `AcademicCalendarBrowserTest.php` — tambah event + muncul di grid

### Fase 6 — Optimasi & CI/CD (Estimasi: 1-2 hari)
- [ ] Jalankan `./vendor/bin/pest --coverage` dan pastikan minimal 80%
- [ ] Setup GitHub Actions workflow untuk autorun test di setiap PR
- [ ] Setup `--parallel` untuk mempercepat CI
- [ ] Review test yang lambat dan optimasi

---

## BAGIAN 13: RISK & NOTES

### Bagian Paling Berisiko Jika Tidak Ditest

1. **`processPromotion()` di ManageClass** — menggunakan DB transaction yang kompleks, melibatkan pivot table `kelas_murid`, update status murid, dan pembuatan kelas baru. Jika gagal tanpa test, data bisa korup.
2. **Role-based authorization** — authorization di project ini murni dilakukan via `abort(403)` di dalam `mount()` Livewire. Tidak ada middleware khusus per route. Risiko: murid bisa mengakses endpoint admin jika guard terlewat.
3. **Validasi nilai 0-100** — dua lapis (Alpine + PHP), tapi pernah ada bug di mana clamping Alpine menutupi error PHP. Test harus memverifikasi keduanya.
4. **Unique constraint pada `nilais`** — 6 kolom unique constraint. Jika tidak ditest, upsert logic bisa menghasilkan duplikat atau error 500 di production.

### Potensi Bottleneck Saat Implementasi Testing

1. **10 factory hilang** — hampir semua Integration dan Feature test bergantung pada factory. Ini harus diselesaikan di Fase 1 sebelum bisa menulis test apapun.
2. **`pest-plugin-livewire` belum terinstall** — semua Livewire component test di Feature layer bergantung pada plugin ini.
3. **Private method** — `authorizeAdmin()`, `resetForm()`, `monthName()` adalah private method. Unit test harus mengujinya secara tidak langsung melalui public method atau menggunakan reflection (tidak disarankan di Pest).
4. **Computed properties** — `#[Computed]` properties di Livewire di-cache per request. Pastikan test menggunakan `Livewire::test()` yang me-reset cache antar assertion.

### Rekomendasi Prioritas Jika Waktu Terbatas

Jika hanya ada waktu terbatas, urutan prioritas minimum:
1. ✅ Fase 1 Foundation (factory + Pest.php) — wajib
2. ✅ Auth Authorization tests — lindungi semua route
3. ✅ `ClassPromotionTest` (Integration) — paling berisiko
4. ✅ `ManageGrade` Livewire test — validasi nilai kritis
5. ✅ `ManageUser` CRUD test — fitur inti admin

### Potensi Konflik dengan Package Lain

| Konflik | Deskripsi | Solusi |
|---|---|---|
| **Livewire Flux** | Komponen `flux:modal` sudah diganti custom modal, tapi ada sisa penggunaan `flux:input`, `flux:button` di beberapa view. Dusk selector mungkin berbeda. | Gunakan `@dusk` attribute eksplisit, jangan bergantung pada selector CSS Flux |
| **Laravel Fortify** | Fortify punya route auth sendiri yang bisa bentrok dengan test route auth. | Gunakan `Fortify::routes()` yang ada, test via Livewire login component bukan endpoint Fortify langsung |
| **Laravel Octane** | Octane memengaruhi app lifecycle. Pastikan test environment tidak menggunakan Octane worker. | Set `OCTANE_SERVER=` (kosong) di `.env.testing` |
| **Sentry** | Sentry bisa mengirim error ke production saat test berjalan. | Set `SENTRY_LARAVEL_DSN=` (kosong) di `.env.testing` |

### Catatan Khusus Dusk sebagai Pengecualian

> Dusk menggunakan class-based test (`extends DuskTestCase`) karena `pest-plugin-dusk` yang stabil untuk Pest v4 belum tersedia per April 2026. Ini adalah **keputusan yang disadari dan terdokumentasi**, bukan kelalaian. Semua file di `tests/Browser/` harus mengikuti konvensi Dusk standar Laravel, termasuk penggunaan `setUp()`, `tearDown()`, dan `@afterClass` untuk ChromeDriver lifecycle.

---

## BAGIAN 14: RINGKASAN ESTIMASI

| Layer | Jumlah File | Estimasi `it()` Block | Prioritas Utama |
|---|---|---|---|
| Unit Test | 8 file | ~28 it() | `AcademicEvent::defaultColor()`, `User::initials()`, `currentTahunAjaran()` |
| Integration Test | 6 file | ~16 it() | `processPromotion()` transaction, nilai unique constraint, rekap kalkulasi |
| Feature Test | 18 file | ~96 it() | Authorization semua route, ManageGrade validasi, AcademicCalendar CRUD |
| E2E Test (Dusk) | 5 file | ~7 test methods | Login, input absensi, input nilai, kenaikan kelas modal |
| **Total** | **37 file** | **~147 test cases** | |

### Distribusi Berdasarkan Prioritas

| Prioritas | Jumlah Test | % dari Total |
|---|---|---|
| 🔴 Critical | ~72 | ~49% |
| 🟡 High | ~49 | ~33% |
| 🟢 Medium | ~19 | ~13% |
| ⚪ Low | ~7 | ~5% |

---

*Generated by Claude Code — Pest Testing Planning v1.0*
