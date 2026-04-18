# Arsitektur Dashboard Al-Iman

> Visualisasi arsitektur sistem manajemen sekolah berbasis Laravel 12 + Livewire 3

---

## 1. Arsitektur Sistem Keseluruhan (High-Level Overview)

```mermaid
graph TB
    subgraph CLIENT["🖥️ Client Layer"]
        BROWSER["Browser\n(Tailwind CSS + Alpine.js)"]
        MOBILE["Mobile Client\n(API Consumer)"]
    end

    subgraph GATEWAY["🔀 Gateway / Web Server"]
        CADDY["Caddy / FrankenPHP\n(TLS Termination + Reverse Proxy)"]
    end

    subgraph APP["⚙️ Application Layer (Laravel 12 + Octane)"]
        direction TB
        WEB["Web Routes\n(Livewire 3 SSR)"]
        API["API Routes\n(REST + Sanctum)"]
        QUEUE["Queue Worker\n(Redis Driver)"]
        FORTIFY["Laravel Fortify\n(Auth + 2FA)"]
        SANCTUM["Laravel Sanctum\n(API Token)"]
    end

    subgraph DATA["🗄️ Data Layer"]
        POSTGRES[("PostgreSQL 17\n(Primary Database)")]
        REDIS[("Redis 7\n(Cache / Session / Queue)")]
        MINIO[("MinIO\n(S3 Object Storage)")]
    end

    subgraph EXTERNAL["🌐 External Services"]
        SENTRY["Sentry\n(Error Tracking)"]
        MAILPIT["Mailpit\n(Mail Testing)"]
    end

    BROWSER -->|"HTTP/HTTPS + WebSocket"| CADDY
    MOBILE -->|"HTTPS + Bearer Token"| CADDY
    CADDY -->|"FastCGI / Worker Mode"| WEB
    CADDY -->|"FastCGI / Worker Mode"| API
    WEB --> FORTIFY
    API --> SANCTUM
    WEB --> POSTGRES
    API --> POSTGRES
    WEB --> REDIS
    QUEUE --> REDIS
    QUEUE --> POSTGRES
    WEB --> MINIO
    APP -->|"Error Reports"| SENTRY
    APP -->|"Send Email"| MAILPIT

    style CLIENT fill:#dbeafe,stroke:#3b82f6
    style GATEWAY fill:#fef3c7,stroke:#f59e0b
    style APP fill:#dcfce7,stroke:#22c55e
    style DATA fill:#fce7f3,stroke:#ec4899
    style EXTERNAL fill:#f3f4f6,stroke:#9ca3af
```

---

## 2. Arsitektur Docker & Infrastruktur

```mermaid
graph LR
    subgraph DOCKER["🐳 Docker Compose Stack"]
        subgraph WEB_CONTAINER["Container: app"]
            OCTANE["Laravel Octane\n(FrankenPHP Workers x2)"]
            SUPERVISOR["Supervisor\n(Process Manager)"]
        end

        subgraph WORKER_CONTAINER["Container: worker"]
            QUEUE_WORKER["Queue Worker\n(php artisan queue:work)"]
        end

        subgraph DB_CONTAINER["Container: postgres"]
            PG[("PostgreSQL 17-Alpine\nPort: 5432")]
        end

        subgraph CACHE_CONTAINER["Container: redis"]
            RD[("Redis 7-Alpine\nPort: 6379")]
        end

        subgraph STORAGE_CONTAINER["Container: minio"]
            MN[("MinIO\nAPI: 9000\nConsole: 9001")]
        end

        subgraph MAIL_CONTAINER["Container: mailpit (dev)"]
            MP["Mailpit\nSMTP: 1025\nUI: 8025"]
        end
    end

    OCTANE -->|"PDO"| PG
    OCTANE -->|"Predis"| RD
    OCTANE -->|"S3 SDK"| MN
    OCTANE -->|"SMTP"| MP
    QUEUE_WORKER -->|"PDO"| PG
    QUEUE_WORKER -->|"Predis"| RD
    SUPERVISOR --> OCTANE

    style DOCKER fill:#f0f9ff,stroke:#0ea5e9
    style WEB_CONTAINER fill:#dcfce7,stroke:#16a34a
    style WORKER_CONTAINER fill:#fef9c3,stroke:#ca8a04
    style DB_CONTAINER fill:#ede9fe,stroke:#7c3aed
    style CACHE_CONTAINER fill:#fee2e2,stroke:#dc2626
    style STORAGE_CONTAINER fill:#ffedd5,stroke:#ea580c
    style MAIL_CONTAINER fill:#f1f5f9,stroke:#64748b
```

---

## 3. Entity Relationship Diagram (ERD)

```mermaid
erDiagram
    User {
        bigint id PK
        string name
        string email
        enum role "admin|guru|murid"
        timestamp email_verified_at
        string password
        boolean two_factor_enabled
    }

    ProfilMurid {
        bigint id PK
        bigint user_id FK
        string nis
        string nama_lengkap
        date tanggal_lahir
        string jenis_kelamin
        string alamat
        string foto
        string tahun_masuk
    }

    ProfilGuru {
        bigint id PK
        bigint user_id FK
        string nip
        string nama_lengkap
        string mata_pelajaran_utama
        string foto
        string status
    }

    Kelas {
        bigint id PK
        bigint wali_kelas_id FK
        string nama_kelas
        string tingkat
        string tahun_ajaran
        string semester
    }

    Subject {
        bigint id PK
        string nama
        string kode
        string kategori
        integer jam_per_minggu
    }

    TeachingSchedule {
        bigint id PK
        bigint profil_guru_id FK
        bigint subject_id FK
        bigint kelas_id FK
        string hari
        time jam_mulai
        time jam_selesai
    }

    PertemuanKelas {
        bigint id PK
        bigint teaching_schedule_id FK
        date tanggal
        string materi
        string catatan
        string status
    }

    Absensi {
        bigint id PK
        bigint pertemuan_kelas_id FK
        bigint profil_murid_id FK
        enum status "hadir|sakit|izin|alpha"
        string keterangan
    }

    Nilai {
        bigint id PK
        bigint profil_murid_id FK
        bigint profil_guru_id FK
        bigint subject_id FK
        bigint kelas_id FK
        decimal nilai_uts
        decimal nilai_uas
        string semester
        string tahun_ajaran
    }

    AcademicEvent {
        bigint id PK
        string judul
        date tanggal_mulai
        date tanggal_selesai
        string kategori
        string warna
    }

    SchoolSetting {
        bigint id PK
        string key
        text value
    }

    KelasMusid {
        bigint kelas_id FK
        bigint profil_murid_id FK
        string tahun_ajaran
    }

    User ||--o| ProfilMurid : "hasOne"
    User ||--o| ProfilGuru : "hasOne"
    ProfilGuru ||--o{ Kelas : "wali kelas"
    ProfilGuru ||--o{ TeachingSchedule : "mengajar"
    ProfilGuru ||--o{ Nilai : "menilai"
    Kelas ||--o{ TeachingSchedule : "memiliki"
    Kelas ||--o{ Nilai : "memiliki"
    Kelas }o--o{ ProfilMurid : "kelas_murid"
    Subject ||--o{ TeachingSchedule : "diajarkan di"
    Subject ||--o{ Nilai : "memiliki"
    TeachingSchedule ||--o{ PertemuanKelas : "memiliki"
    PertemuanKelas ||--o{ Absensi : "mencatat"
    ProfilMurid ||--o{ Absensi : "memiliki"
    ProfilMurid ||--o{ Nilai : "memiliki"
```

---

## 4. Alur Autentikasi & Otorisasi

```mermaid
flowchart TD
    START([User Akses Aplikasi]) --> CHECK_AUTH{Sudah Login?}

    CHECK_AUTH -->|Tidak| LOGIN_PAGE[Halaman Login]
    CHECK_AUTH -->|Ya| CHECK_ROLE{Cek Role}

    LOGIN_PAGE --> INPUT_CRED[Input Email + Password]
    INPUT_CRED --> RATE_LIMIT{Rate Limit\n5x/menit?}
    RATE_LIMIT -->|Terlampaui| BLOCKED[429 Too Many Requests]
    RATE_LIMIT -->|OK| VERIFY_CRED{Kredensial Valid?}

    VERIFY_CRED -->|Tidak| LOGIN_FAIL[Error: Kredensial Salah]
    LOGIN_FAIL --> LOGIN_PAGE

    VERIFY_CRED -->|Ya| CHECK_2FA{2FA Aktif?}
    CHECK_2FA -->|Ya| OTP_PAGE[Input OTP Code]
    OTP_PAGE --> VERIFY_OTP{OTP Valid?}
    VERIFY_OTP -->|Tidak| OTP_FAIL[Error: Kode Salah]
    VERIFY_OTP -->|Ya| CREATE_SESSION

    CHECK_2FA -->|Tidak| CREATE_SESSION[Buat Session Laravel]
    CREATE_SESSION --> CHECK_ROLE

    CHECK_ROLE -->|admin| ADMIN_DASH["Dashboard Admin\n✓ Kelola User\n✓ Kelola Kelas\n✓ Kelola Jadwal\n✓ Rekap Nilai\n✓ Pengaturan Sekolah"]
    CHECK_ROLE -->|guru| GURU_DASH["Dashboard Guru\n✓ Jadwal Mengajar\n✓ Absensi Murid\n✓ Input Nilai\n✓ Pertemuan Kelas"]
    CHECK_ROLE -->|murid| MURID_DASH["Dashboard Murid\n✓ Jadwal Pelajaran\n✓ Absensi Diri\n✓ Lihat Nilai\n✓ Kalender Akademik"]

    subgraph API_FLOW["Alur API (Mobile)"]
        API_REQ[POST /api/login] --> SANCTUM[Sanctum Token Auth]
        SANCTUM --> TOKEN[Personal Access Token]
        TOKEN --> API_ENDPOINT["GET /api/student/dashboard\nGET /api/user"]
    end

    style START fill:#22c55e,color:#fff
    style ADMIN_DASH fill:#3b82f6,color:#fff
    style GURU_DASH fill:#8b5cf6,color:#fff
    style MURID_DASH fill:#f59e0b,color:#fff
    style BLOCKED fill:#ef4444,color:#fff
```

---

## 5. Komponen Livewire & Hak Akses (Role-Based)

```mermaid
graph TB
    subgraph SHARED["Komponen Bersama (Semua Role)"]
        DASH["Dashboard\n(role-aware stats)"]
        LOGIN["Auth/Login"]
        SETTINGS["Settings/\nProfile · Password · 2FA · Appearance"]
    end

    subgraph ADMIN_COMP["Komponen Admin Eksklusif"]
        M_USER["ManageUser\n(CRUD akun)"]
        M_CLASS["ManageClass\n(CRUD kelas)"]
        M_SUBJECT["ManageSubject\n(CRUD mata pelajaran)"]
        M_SCHEDULE["ManageSchedule\n(CRUD jadwal mengajar)"]
        M_STUDENT["ManageStudent\n(CRUD profil murid)"]
        M_SETTINGS["ManageSettings\n(pengaturan sekolah)"]
        ADMIN_GRADE["AdminGradeRecap\n(rekap nilai semua kelas)"]
        ATT_RECAP["AttendanceRecap\n(rekap absensi)"]
    end

    subgraph GURU_COMP["Komponen Guru"]
        MANAGE_GRADE["ManageGrade\n(input nilai murid)"]
        TEACHER_ATT["TeacherAttendance\n(absensi murid per sesi)"]
    end

    subgraph MURID_COMP["Komponen Murid"]
        STUDENT_GRADE["StudentGrade\n(lihat nilai sendiri)"]
        ATT_VIEW["Attendance\n(lihat absensi sendiri)"]
    end

    ADMIN -->|"akses"| SHARED
    ADMIN -->|"akses"| ADMIN_COMP
    ADMIN -->|"akses"| GURU_COMP
    ADMIN -->|"akses"| MURID_COMP

    GURU -->|"akses"| SHARED
    GURU -->|"akses"| GURU_COMP

    MURID -->|"akses"| SHARED
    MURID -->|"akses"| MURID_COMP

    style ADMIN_COMP fill:#dbeafe,stroke:#3b82f6
    style GURU_COMP fill:#ede9fe,stroke:#7c3aed
    style MURID_COMP fill:#fef3c7,stroke:#f59e0b
    style SHARED fill:#dcfce7,stroke:#16a34a
```

---

## 6. Alur Request Web (Livewire Full-Stack)

```mermaid
sequenceDiagram
    actor User as 👤 User
    participant Browser as Browser (Alpine.js)
    participant Livewire as Livewire 3
    participant MW as Middleware Stack
    participant Component as Livewire Component
    participant DB as PostgreSQL
    participant Cache as Redis Cache

    User->>Browser: Interaksi UI (klik, input)
    Browser->>Livewire: AJAX Request (wire:click / wire:model)
    Livewire->>MW: HTTP Request ke /livewire/update
    MW->>MW: auth · throttle · csrf
    MW->>Component: Dispatch ke Component Method

    alt Data dari Cache
        Component->>Cache: Cache::get()
        Cache-->>Component: Hit ✓ (return data)
    else Cache Miss
        Component->>DB: Eloquent Query (with eager loading)
        DB-->>Component: Collection / Model
        Component->>Cache: Cache::put()
    end

    Component-->>Livewire: Re-render HTML diff
    Livewire-->>Browser: JSON Morphdom Patch
    Browser-->>User: DOM Update (tanpa reload)

    Note over Component,DB: N+1 dicegah dengan with()<br/>eager loading di semua relasi
```

---

## 7. Stack Teknologi Summary

```mermaid
mindmap
  root((Dashboard\nAl-Iman))
    Backend
      Laravel 12
        PHP 8.4/8.5
        Eloquent ORM
        Laravel Queue
      Laravel Octane
        FrankenPHP
        2 Workers
        1000 req/worker
      Auth
        Fortify
          Session Login
          2FA TOTP
          Email Verification
        Sanctum
          API Token
    Frontend
      Livewire 3
        21 Komponen
        Real-time SSR
      Alpine.js
        Minimal JS
      Tailwind CSS 4
        Flux UI Components
      Vite
        Hot Module Reload
    Data
      PostgreSQL 17
        11 Models
        19 Migrations
      Redis 7
        Cache
        Session
        Queue
      MinIO
        S3 Object Storage
    Infrastructure
      Docker Compose
        Dev Config
        Prod Config
      Caddy
        TLS Auto
        Reverse Proxy
      Supervisor
        Worker Manager
    Monitoring
      Sentry
        Error Tracking
      Mailpit
        Mail Testing Dev
    Testing
      Pest PHP
        Unit Tests
        Feature Tests
        Integration Tests
```
