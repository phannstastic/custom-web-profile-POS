import pytest
from pages.public_menu_page import PublicMenuPage

@pytest.mark.pub
def test_pub_001_home_navigation(driver, base_url):
    """TC-PUB-001: Verifikasi Tampilan Beranda & Navigasi Publik"""
    driver.get(base_url)
    assert "404" not in driver.title
    # Pastikan tidak ada teks template Kang Naryo yang tersisa
    page_source = driver.page_source.lower()
    assert "kang naryo" not in page_source

@pytest.mark.pub
def test_pub_002_menu_modal_carousel(driver, base_url):
    """TC-PUB-002: Buka Modal Pop-Up Menu & Navigasi Slider Multi-Foto"""
    menu_page = PublicMenuPage(driver)
    menu_page.open(base_url)
    
    # Pastikan ada menu produk yang tampil
    cards_count = menu_page.get_menu_cards_count()
    assert cards_count > 0, "Daftar menu harus memiliki minimal 1 produk"

    # Klik kartu produk pertama untuk membuka pop-up modal
    menu_page.click_first_menu_card()
    
    # Verifikasi modal terbuka
    assert menu_page.is_modal_displayed(), "Modal pop-up detail menu harus terbuka saat kartu diklik"
    title = menu_page.get_modal_item_title()
    assert len(title) > 0, "Judul menu di modal tidak boleh kosong"
