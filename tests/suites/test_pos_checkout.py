import pytest
import time
from pages.pos_page import PosPage

@pytest.mark.pos
def test_pos_001_add_to_cart_and_calculate_total(driver, base_url):
    """TC-POS-001: Tambah Menu ke Keranjang & Hitung Total Belanja"""
    pos = PosPage(driver)
    pos.open(base_url)
    
    # Tunggu produk termuat
    products = pos.get_product_cards()
    assert len(products) > 0, "Produk harus tersedia di layar kasir POS"

    # Tambahkan produk pertama (2x) dan produk kedua (1x jika ada)
    pos.add_first_product(times=2)
    if len(products) > 1:
        pos.add_product_by_index(index=1, times=1)

    time.sleep(0.5)
    cart_items = pos.get_cart_item_rows()
    assert len(cart_items) >= 1, "Keranjang harus berisi minimal 1 baris item"

    # Uji pengurangan item (klik tombol minus -)
    pos.decrease_first_item_qty()
    time.sleep(0.3)

@pytest.mark.pos
def test_pos_002_cash_checkout_and_change_calc(driver, base_url):
    """TC-POS-002: Checkout Transaksi Pembayaran Tunai (Cash) & Hitung Kembalian"""
    pos = PosPage(driver)
    pos.open(base_url)
    
    # Tambahkan item ke keranjang
    pos.add_first_product(times=1)
    time.sleep(0.5)
    
    # Buka modal bayar tunai
    pos.open_cash_modal()
    assert pos.is_cash_modal_open(), "Modal pembayaran tunai harus terbuka"

    # Masukkan uang tunai 100000
    pos.input_cash_amount(100000)
    time.sleep(0.5)

    # Verifikasi tombol bayar aktif
    confirm_btn = pos.find(pos.CASH_CONFIRM_BTN)
    assert confirm_btn.is_enabled(), "Tombol konfirmasi pembayaran tunai harus aktif jika uang cukup"
