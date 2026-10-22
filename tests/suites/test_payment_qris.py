import pytest
import time
from pages.pos_page import PosPage

@pytest.mark.pay
def test_pay_001_initiate_qris_payment(driver, base_url):
    """TC-PAY-001: Inisiasi Pembayaran QRIS Midtrans & Panggilan Tombol"""
    pos = PosPage(driver)
    pos.open(base_url)

    # Tambah produk ke keranjang
    pos.add_first_product(times=1)
    time.sleep(0.5)

    # Klik tombol bayar QRIS
    pos.click_qris_button()
    time.sleep(2)

    # Verifikasi Snap iframe Midtrans muncul di layar atau tombol cancel qris aktif
    snap_iframes = driver.find_elements("id", "snap-midtrans")
    assert len(snap_iframes) > 0 or pos.is_cancel_qris_displayed(), "Snap popup Midtrans atau tombol QRIS harus terpicu"

@pytest.mark.pay
def test_pay_002_cancel_qris_no_button_freeze(driver, base_url):
    """TC-PAY-002: Batalkan Pembayaran QRIS Tanpa Error Tombol Loading"""
    pos = PosPage(driver)
    pos.open(base_url)

    # Tambah produk ke keranjang
    pos.add_first_product(times=1)
    time.sleep(0.5)

    # Inisiasi pembayaran QRIS
    pos.click_qris_button()
    time.sleep(2)

    # Jika Snap iframe menutupi layar, tutup melalui script / remove popup
    driver.execute_script("""
        const snap = document.getElementById('snap-midtrans');
        if (snap) snap.remove();
        // Trigger cancelQrisPayment di Alpine component jika tersedia
        const el = document.querySelector('[x-data]');
        if (el && el._x_dataStack && el._x_dataStack[0].cancelQrisPayment) {
            el._x_dataStack[0].cancelQrisPayment();
        }
    """)
    time.sleep(0.5)

    # Verifikasi tombol bayar QRIS dan Bayar Tunai kembali normal & bisa diklik
    assert pos.is_visible(pos.QRIS_PAY_BUTTON), "Tombol Bayar QRIS harus kembali tersedia"
    assert pos.is_visible(pos.CASH_PAY_BUTTON), "Tombol Bayar Tunai harus kembali tersedia"
