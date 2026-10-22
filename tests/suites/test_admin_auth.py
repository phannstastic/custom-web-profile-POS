import pytest
from pages.admin_login_page import AdminLoginPage

@pytest.mark.adm
def test_adm_002_login_failed_invalid_credentials(driver, base_url):
    """TC-ADM-002: Login Backoffice Admin Gagal (Invalid Credentials)"""
    login_page = AdminLoginPage(driver)
    login_page.open(base_url)
    
    # Masukkan user/password yang salah
    login_page.login("admin", "passwordsalah123")
    
    # Verifikasi tetap di login page dan alert error muncul
    assert "/admin/login" in driver.current_url or "/admin" in driver.current_url
    assert login_page.is_error_displayed(), "Pesan error merah harus muncul saat kredensial salah"

@pytest.mark.adm
def test_adm_001_login_success(driver, base_url):
    """TC-ADM-001: Login Backoffice Admin Sukses"""
    login_page = AdminLoginPage(driver)
    login_page.open(base_url)
    
    # Masukkan username dan password benar
    login_page.login("admin", "admin123")
    
    # Tunggu browser selesai redirect keluar dari /admin/login ke /admin
    from selenium.webdriver.support.ui import WebDriverWait
    from selenium.webdriver.support import expected_conditions as EC
    WebDriverWait(driver, 10).until(lambda d: d.current_url.rstrip('/').endswith('/admin'))
    
    assert "/admin" in driver.current_url
    assert "Login" not in driver.title
    # Pastikan nama admin tampil di sidebar
    assert "Administrator" in driver.page_source
