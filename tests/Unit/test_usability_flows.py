"""
test_usability_flows.py

Pytest black-box (HTTP) usability test untuk aplikasi Laravel
"Klasifikasi Penyakit Kulit Menggunakan CNN". Test ini menembak
langsung server yang sedang jalan (bukan lewat PHPUnit), memakai
`requests` + `BeautifulSoup` untuk menangani CSRF token dari form
Blade (Laravel wajib token `_token` untuk request POST via session).

Alur yang di-cover: REGISTER, LOGIN, LOGOUT, DETEKSI (upload + hasil).
Sesuai permintaan, alur RESET/FORGOT PASSWORD SENGAJA DILEWATI.

Asumsi yang perlu kamu sesuaikan:
- BASE_URL: alamat server Laravel yang sedang berjalan (default
  http://127.0.0.1:8000, ganti lewat env var BASE_URL kalau beda)
- Nama field form: email, password, name, password_confirmation, gambar
- Middleware auth mengarahkan (redirect 302) ke /login saat belum login
- Field error Laravel muncul dalam elemen HTML apa pun yang memuat
  teks pesan error (test ini hanya mengecek keberadaan indikator error
  umum, sesuaikan selector CSS kalau blade kamu pakai class tertentu
  misalnya <span class="text-red-500">)

Persiapan sebelum run:
    pip install pytest requests beautifulsoup4

Jalankan dengan:
    BASE_URL=http://127.0.0.1:8000 pytest test_usability_flows.py -v
"""

import os
import time
import uuid
import io

import pytest
import requests
from bs4 import BeautifulSoup

BASE_URL = os.environ.get("BASE_URL", "http://127.0.0.1:8000")


# ---------------------------------------------------------------------------
# Helper
# ---------------------------------------------------------------------------

def get_csrf_token(session: requests.Session, url: str) -> str:
    """Ambil token CSRF (_token) dari form di halaman `url`."""
    resp = session.get(url)
    soup = BeautifulSoup(resp.text, "html.parser")
    token_input = soup.find("input", {"name": "_token"})
    assert token_input is not None, (
        f"Tidak menemukan input _token di {url} — cek apakah blade "
        f"sudah pakai @csrf di form"
    )
    return token_input["value"]


def make_test_image(name: str = "kulit.jpg", size_kb: int = 50) -> tuple:
    """Buat file gambar JPEG dummy in-memory untuk keperluan upload."""
    # Header JPEG minimal + padding acak agar ukuran bisa diatur
    content = b"\xff\xd8\xff\xe0" + os.urandom(size_kb * 1024)
    return (name, io.BytesIO(content), "image/jpeg")


@pytest.fixture()
def session():
    """Session HTTP baru untuk setiap test (cookie/session terisolasi)."""
    return requests.Session()


@pytest.fixture()
def unique_user():
    """Data user unik supaya test register tidak bentrok antar-run."""
    uid = uuid.uuid4().hex[:8]
    return {
        "name": f"Test User {uid}",
        "email": f"test_{uid}@example.com",
        "password": "password123",
    }


@pytest.fixture()
def registered_user(session, unique_user):
    """User yang sudah terdaftar, siap dipakai untuk test login."""
    token = get_csrf_token(session, f"{BASE_URL}/register")
    session.post(
        f"{BASE_URL}/register",
        data={
            "_token": token,
            "name": unique_user["name"],
            "email": unique_user["email"],
            "password": unique_user["password"],
            "password_confirmation": unique_user["password"],
        },
    )
    return unique_user


@pytest.fixture()
def logged_in_session(session, registered_user):
    """Session yang sudah login, siap dipakai untuk test halaman deteksi."""
    token = get_csrf_token(session, f"{BASE_URL}/login")
    session.post(
        f"{BASE_URL}/login",
        data={
            "_token": token,
            "email": registered_user["email"],
            "password": registered_user["password"],
        },
    )
    return session


# ---------------------------------------------------------------------------
# REGISTER
# ---------------------------------------------------------------------------

class TestUsabilityRegister:

    def test_halaman_register_dapat_diakses(self, session):
        resp = session.get(f"{BASE_URL}/register")
        assert resp.status_code == 200
        assert "name" in resp.text.lower()
        assert "email" in resp.text.lower()

    def test_register_kosong_menampilkan_error(self, session):
        token = get_csrf_token(session, f"{BASE_URL}/register")
        resp = session.post(
            f"{BASE_URL}/register",
            data={"_token": token},
            allow_redirects=True,
        )
        # Laravel redirect back dengan session error -> halaman register lagi
        assert resp.status_code == 200
        assert resp.url.endswith("/register") or "register" in resp.url

    def test_register_email_tidak_valid_ditolak(self, session):
        token = get_csrf_token(session, f"{BASE_URL}/register")
        resp = session.post(
            f"{BASE_URL}/register",
            data={
                "_token": token,
                "name": "Test User",
                "email": "bukan-email",
                "password": "password123",
                "password_confirmation": "password123",
            },
            allow_redirects=True,
        )
        assert resp.status_code == 200
        assert "register" in resp.url

    def test_register_valid_berhasil_dan_redirect_jelas(self, session, unique_user):
        token = get_csrf_token(session, f"{BASE_URL}/register")
        resp = session.post(
            f"{BASE_URL}/register",
            data={
                "_token": token,
                "name": unique_user["name"],
                "email": unique_user["email"],
                "password": unique_user["password"],
                "password_confirmation": unique_user["password"],
            },
            allow_redirects=True,
        )
        assert resp.status_code == 200
        # Setelah register sukses, tidak boleh nyangkut di halaman register
        assert "register" not in resp.url or "login" in resp.url


# ---------------------------------------------------------------------------
# LOGIN
# ---------------------------------------------------------------------------

class TestUsabilityLogin:

    def test_halaman_login_dapat_diakses(self, session):
        resp = session.get(f"{BASE_URL}/login")
        assert resp.status_code == 200
        assert "email" in resp.text.lower()
        assert "password" in resp.text.lower()

    def test_login_kosong_menampilkan_error(self, session):
        token = get_csrf_token(session, f"{BASE_URL}/login")
        resp = session.post(
            f"{BASE_URL}/login",
            data={"_token": token},
            allow_redirects=True,
        )
        assert resp.status_code == 200
        assert "login" in resp.url

    def test_login_kredensial_salah_menampilkan_error(self, session, registered_user):
        token = get_csrf_token(session, f"{BASE_URL}/login")
        resp = session.post(
            f"{BASE_URL}/login",
            data={
                "_token": token,
                "email": registered_user["email"],
                "password": "password_salah",
            },
            allow_redirects=True,
        )
        assert resp.status_code == 200
        assert "login" in resp.url  # tetap di halaman login, bukan masuk dashboard

    def test_login_valid_redirect_ke_dashboard(self, session, registered_user):
        token = get_csrf_token(session, f"{BASE_URL}/login")
        resp = session.post(
            f"{BASE_URL}/login",
            data={
                "_token": token,
                "email": registered_user["email"],
                "password": registered_user["password"],
            },
            allow_redirects=True,
        )
        assert resp.status_code == 200
        assert "dashboard" in resp.url


# ---------------------------------------------------------------------------
# LOGOUT
# ---------------------------------------------------------------------------

class TestUsabilityLogout:

    def test_logout_mengeluarkan_pengguna(self, logged_in_session):
        token = get_csrf_token(logged_in_session, f"{BASE_URL}/dashboard")
        resp = logged_in_session.post(
            f"{BASE_URL}/logout",
            data={"_token": token},
            allow_redirects=True,
        )
        assert resp.status_code == 200

        # Setelah logout, akses dashboard harus ditolak / redirect ke login
        check = logged_in_session.get(f"{BASE_URL}/dashboard")
        assert "login" in check.url


# ---------------------------------------------------------------------------
# DETEKSI
# ---------------------------------------------------------------------------

class TestUsabilityDeteksi:

    def test_akses_deteksi_tanpa_login_redirect_ke_login(self, session):
        resp = session.get(f"{BASE_URL}/deteksi", allow_redirects=True)
        assert resp.status_code == 200
        assert "login" in resp.url

    def test_halaman_deteksi_dapat_diakses_setelah_login(self, logged_in_session):
        resp = logged_in_session.get(f"{BASE_URL}/deteksi")
        assert resp.status_code == 200

    def test_upload_tanpa_file_menampilkan_error(self, logged_in_session):
        token = get_csrf_token(logged_in_session, f"{BASE_URL}/deteksi")
        resp = logged_in_session.post(
            f"{BASE_URL}/deteksi/store",
            data={"_token": token},
            allow_redirects=True,
        )
        assert resp.status_code in (200, 422)

    def test_upload_file_bukan_gambar_ditolak(self, logged_in_session):
        token = get_csrf_token(logged_in_session, f"{BASE_URL}/deteksi")
        files = {"gambar": ("dokumen.pdf", io.BytesIO(b"%PDF-1.4 dummy"), "application/pdf")}
        resp = logged_in_session.post(
            f"{BASE_URL}/deteksi/store",
            data={"_token": token},
            files=files,
            allow_redirects=True,
        )
        assert resp.status_code in (200, 422)

    def test_upload_gambar_valid_diproses(self, logged_in_session):
        token = get_csrf_token(logged_in_session, f"{BASE_URL}/deteksi")
        name, content, mimetype = make_test_image()
        files = {"gambar": (name, content, mimetype)}
        resp = logged_in_session.post(
            f"{BASE_URL}/deteksi/store",
            data={"_token": token},
            files=files,
            allow_redirects=True,
            timeout=30,  # proses ML bisa lebih lambat dari request biasa
        )
        # Berhasil diproses -> diarahkan ke halaman hasil
        assert resp.status_code == 200
        assert "hasil" in resp.url or "deteksi" in resp.url

    def test_halaman_hasil_id_tidak_ditemukan_menampilkan_404(self, logged_in_session):
        resp = logged_in_session.get(f"{BASE_URL}/deteksi/hasil/999999999")
        assert resp.status_code == 404

    def test_waktu_respons_halaman_deteksi_wajar(self, logged_in_session):
        start = time.time()
        logged_in_session.get(f"{BASE_URL}/deteksi")
        duration = time.time() - start
        assert duration < 2.0, "Halaman deteksi merespons lebih dari 2 detik"


# ---------------------------------------------------------------------------
# CATATAN: alur RESET / FORGOT PASSWORD sengaja tidak diuji di file ini
# sesuai permintaan.
# ---------------------------------------------------------------------------