import pytest
import time
from pages.admin_login_page import AdminLoginPage
from pages.admin_products_page import AdminProductsPage
from pages.admin_settings_page import AdminSettingsPage
from selenium.webdriver.support.ui import WebDriverWait

@pytest.mark.adm
def test_adm_003_add_product_modal_form(driver, base_url):
    """TC-ADM-003: Form Tambah Menu Baru & Input Validasi"""
    # Login dulu
    login = AdminLoginPage(driver)
    login.open(base_url)
    login.login("admin", "admin123")
    WebDriverWait(driver, 10).until(lambda d: d.current_url.rstrip('/').endswith('/admin'))

    # Buka produk
    products_page = AdminProductsPage(driver)
    products_page.open(base_url)

    # Klik tambah menu
    products_page.click_add_menu()
    assert products_page.is_modal_open(), "Modal tambah menu baru harus terbuka"

    # Isi form produk
    products_page.fill_form("Menu Uji Otomasi", 15000, "Deskripsi pengujian otomatis")
    time.sleep(0.5)
    assert products_page.find(products_page.SUBMIT_BTN).is_enabled()

@pytest.mark.adm
def test_adm_004_store_narrative_and_branding(driver, base_url):
    """TC-ADM-004: Personalisasi Identitas Toko & Cerita Narasi Web Profil"""
    # Login
    login = AdminLoginPage(driver)
    login.open(base_url)
    login.login("admin", "admin123")
    WebDriverWait(driver, 10).until(lambda d: d.current_url.rstrip('/').endswith('/admin'))

    # Buka settings
    settings_page = AdminSettingsPage(driver)
    settings_page.open(base_url)

    # Periksa form kustomisasi identitas
    store_name_input = settings_page.find(settings_page.STORE_NAME_INPUT)
    assert store_name_input.get_attribute("value") != "", "Nama toko tidak boleh kosong"
    assert settings_page.find(settings_page.SUBMIT_BTN).is_displayed()
