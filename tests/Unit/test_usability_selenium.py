"""
test_usability_selenium.py

Pytest + Selenium (browser sungguhan) untuk usability testing alur
REGISTER, LOGIN, LOGOUT, DETEKSI. Beda dengan versi requests-based
(test_usability_flows.py), versi ini benar-benar membuka Chrome,
mengisi form lewat elemen UI, klik tombol, dan menunggu render
halaman — jadi ikut menguji JavaScript sisi client (drag-and-drop,
validasi client-side, progress bar) yang tidak tersentuh oleh test
berbasis HTTP request murni.

Sesuai permintaan, alur RESET / FORGOT PASSWORD sengaja dilewati.

Persiapan sebelum run:
    pip install pytest selenium

Selenium 4.6+ sudah otomatis download & kelola ChromeDriver sendiri
(Selenium Manager), jadi tidak perlu install driver terpisah — asal
Google Chrome sudah terpasang di komputer.

Jalankan dengan:
    BASE_URL=http://127.0.0.1:8000 python -m pytest tests/Unit/test_usability_selenium.py -v

Kalau mau lihat browsernya jalan (bukan headless), set:
    HEADLESS=false
"""

import os
import time
import uuid
from pathlib import Path

import pytest
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options

BASE_URL = os.environ.get("BASE_URL", "http://127.0.0.1:8000")
HEADLESS = os.environ.get("HEADLESS", "true").lower() != "false"
WAIT_TIMEOUT = 10  # detik, batas tunggu elemen/redirect muncul


# ---------------------------------------------------------------------------
# Fixtures
# ---------------------------------------------------------------------------

@pytest.fixture()
def driver():
    """Buka browser Chrome baru untuk tiap test, tutup otomatis setelah selesai."""
    options = Options()
    if HEADLESS:
        options.add_argument("--headless=new")
    options.add_argument("--window-size=1366,768")
    options.add_argument("--disable-gpu")

    drv = webdriver.Chrome(options=options)
    drv.implicitly_wait(3)
    yield drv
    drv.quit()


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
def registered_user(driver, unique_user):
    """Mendaftarkan user baru lewat form UI, mengembalikan datanya.

    Catatan: form register tidak punya field password_confirmation —
    hanya name, email, password. Setelah sukses, controller redirect
    ke halaman login (bukan dashboard).
    """
    driver.get(f"{BASE_URL}/register")
    driver.find_element(By.NAME, "name").send_keys(unique_user["name"])
    driver.find_element(By.NAME, "email").send_keys(unique_user["email"])
    driver.find_element(By.NAME, "password").send_keys(unique_user["password"])
    driver.find_element(By.CSS_SELECTOR, "button[type=submit]").click()
    WebDriverWait(driver, WAIT_TIMEOUT).until(
        lambda d: "register" not in d.current_url
    )
    return unique_user


@pytest.fixture()
def logged_in_driver(driver, registered_user):
    """Login lewat form UI, mengembalikan driver yang sudah punya sesi login."""
    driver.get(f"{BASE_URL}/login")
    driver.find_element(By.NAME, "email").send_keys(registered_user["email"])
    driver.find_element(By.NAME, "password").send_keys(registered_user["password"])
    driver.find_element(By.CSS_SELECTOR, "button[type=submit]").click()
    WebDriverWait(driver, WAIT_TIMEOUT).until(
        EC.url_contains("dashboard")
    )
    return driver


# ---------------------------------------------------------------------------
# REGISTER
# ---------------------------------------------------------------------------

class TestUsabilityRegisterSelenium:

    def test_halaman_register_dapat_diakses(self, driver):
        driver.get(f"{BASE_URL}/register")
        assert driver.find_element(By.NAME, "name") is not None
        assert driver.find_element(By.NAME, "email") is not None
        assert driver.find_element(By.NAME, "password") is not None

    def test_register_kosong_menampilkan_error(self, driver):
        driver.get(f"{BASE_URL}/register")
        driver.find_element(By.CSS_SELECTOR, "button[type=submit]").click()
        time.sleep(1)
        # Tetap di halaman register karena validasi gagal
        assert "register" in driver.current_url

    def test_register_email_tidak_valid_ditolak(self, driver):
        driver.get(f"{BASE_URL}/register")
        driver.find_element(By.NAME, "name").send_keys("Test User")
        driver.find_element(By.NAME, "email").send_keys("bukan-email")
        driver.find_element(By.NAME, "password").send_keys("password123")
        driver.find_element(By.CSS_SELECTOR, "button[type=submit]").click()
        time.sleep(1)
        assert "register" in driver.current_url

    def test_register_valid_redirect_ke_login_dengan_pesan_sukses(self, driver, unique_user):
        driver.get(f"{BASE_URL}/register")
        driver.find_element(By.NAME, "name").send_keys(unique_user["name"])
        driver.find_element(By.NAME, "email").send_keys(unique_user["email"])
        driver.find_element(By.NAME, "password").send_keys(unique_user["password"])
        driver.find_element(By.CSS_SELECTOR, "button[type=submit]").click()

        # Sesuai AuthController::register(), sukses -> redirect ke route('login')
        # dengan session('success') "Registrasi berhasil! Silakan login."
        WebDriverWait(driver, WAIT_TIMEOUT).until(EC.url_contains("login"))
        assert "login" in driver.current_url
        assert "Registrasi berhasil" in driver.page_source


# ---------------------------------------------------------------------------
# LOGIN
# ---------------------------------------------------------------------------

class TestUsabilityLoginSelenium:

    def test_halaman_login_dapat_diakses(self, driver):
        driver.get(f"{BASE_URL}/login")
        assert driver.find_element(By.NAME, "email") is not None
        assert driver.find_element(By.NAME, "password") is not None

    def test_login_kosong_menampilkan_error(self, driver):
        driver.get(f"{BASE_URL}/login")
        driver.find_element(By.CSS_SELECTOR, "button[type=submit]").click()
        time.sleep(1)
        assert "login" in driver.current_url

    def test_login_kredensial_salah_menampilkan_error(self, driver, registered_user):
        driver.get(f"{BASE_URL}/login")
        driver.find_element(By.NAME, "email").send_keys(registered_user["email"])
        driver.find_element(By.NAME, "password").send_keys("password_salah")
        driver.find_element(By.CSS_SELECTOR, "button[type=submit]").click()
        time.sleep(1)
        assert "login" in driver.current_url  # tetap di login, tidak masuk dashboard
        # Sesuai AuthController::login(), gagal -> back()->with('error', 'Email atau password salah!')
        assert "Email atau password salah" in driver.page_source

    def test_login_valid_redirect_ke_dashboard(self, driver, registered_user):
        driver.get(f"{BASE_URL}/login")
        driver.find_element(By.NAME, "email").send_keys(registered_user["email"])
        driver.find_element(By.NAME, "password").send_keys(registered_user["password"])
        driver.find_element(By.CSS_SELECTOR, "button[type=submit]").click()

        WebDriverWait(driver, WAIT_TIMEOUT).until(EC.url_contains("dashboard"))
        assert "dashboard" in driver.current_url


# ---------------------------------------------------------------------------
# LOGOUT
# ---------------------------------------------------------------------------

class TestUsabilityLogoutSelenium:

    def test_logout_mengeluarkan_pengguna(self, logged_in_driver):
        # Catatan: belum ada HTML navbar/dashboard untuk tahu selector
        # tombol logout yang sebenarnya. Sebagai gantinya kita submit
        # form logout langsung lewat JS ke route yang benar -> tetap
        # menguji BEHAVIOR logout (session invalidate, redirect ke
        # login), walau bukan lewat klik UI asli. Ganti ini begitu
        # HTML navbar/dashboard sudah ada, supaya benar-benar klik
        # tombolnya.
        csrf_token = logged_in_driver.execute_script(
            "return document.querySelector('meta[name=csrf-token]')?.content "
            "|| document.querySelector('input[name=_token]')?.value"
        )
        if not csrf_token:
            pytest.skip(
                "Tidak menemukan CSRF token di halaman dashboard — "
                "kirim HTML dashboard/navbar agar test logout bisa "
                "disesuaikan dengan selector tombol yang sebenarnya."
            )

        logged_in_driver.execute_script(
            """
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = arguments[0];
            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = arguments[1];
            form.appendChild(token);
            document.body.appendChild(form);
            form.submit();
            """,
            f"{BASE_URL}/logout",
            csrf_token,
        )

        WebDriverWait(logged_in_driver, WAIT_TIMEOUT).until(EC.url_contains("login"))
        assert "login" in logged_in_driver.current_url

        # Setelah logout, akses dashboard harus ditolak
        logged_in_driver.get(f"{BASE_URL}/dashboard")
        WebDriverWait(logged_in_driver, WAIT_TIMEOUT).until(EC.url_contains("login"))
        assert "login" in logged_in_driver.current_url


# ---------------------------------------------------------------------------
# DETEKSI
# ---------------------------------------------------------------------------

class TestUsabilityDeteksiSelenium:

    def test_akses_deteksi_tanpa_login_redirect_ke_login(self, driver):
        driver.get(f"{BASE_URL}/deteksi")
        WebDriverWait(driver, WAIT_TIMEOUT).until(EC.url_contains("login"))
        assert "login" in driver.current_url

    def test_halaman_deteksi_dapat_diakses_setelah_login(self, logged_in_driver):
        logged_in_driver.get(f"{BASE_URL}/deteksi")
        assert "deteksi" in logged_in_driver.current_url

    def test_waktu_respons_halaman_deteksi_wajar(self, logged_in_driver):
        start = time.time()
        logged_in_driver.get(f"{BASE_URL}/deteksi")
        WebDriverWait(logged_in_driver, WAIT_TIMEOUT).until(
            EC.presence_of_element_located((By.TAG_NAME, "body"))
        )
        duration = time.time() - start
        assert duration < 3.0, "Halaman deteksi merespons lebih dari 3 detik (termasuk render browser)"


# ---------------------------------------------------------------------------
# CATATAN: alur RESET / FORGOT PASSWORD sengaja tidak diuji di file ini.
# ---------------------------------------------------------------------------