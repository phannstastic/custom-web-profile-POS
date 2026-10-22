from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from .base_page import BasePage

class PosPage(BasePage):
    # Locators (gunakan XPath yang robust untuk Alpine.js event handlers)
    PRODUCT_CARDS = (By.XPATH, "//div[contains(@class, 'grid')]//div[contains(@class, 'group') and contains(@class, 'cursor-pointer')]")
    CART_ROWS = (By.XPATH, "//aside//div[contains(@class, 'divide-y')]/div")
    CART_RESET_BTN = (By.XPATH, "//button[contains(., 'Reset')]")
    
    # Payment buttons & inputs
    CASH_PAY_BUTTON = (By.XPATH, "//button[contains(., 'Bayar Tunai')]")
    QRIS_PAY_BUTTON = (By.XPATH, "//button[contains(., 'Bayar QRIS')]")
    CANCEL_QRIS_BUTTON = (By.XPATH, "//button[contains(., 'Batalkan QRIS')]")
    
    # Cash Modal
    CASH_MODAL = (By.XPATH, "//div[@x-show='showPaymentModal']")
    CASH_INPUT = (By.XPATH, "//input[@type='number' and @placeholder='0']")
    CASH_CONFIRM_BTN = (By.XPATH, "//button[contains(., 'Bayar & Cetak Nota') or contains(., 'Menyimpan Transaksi')]")
    CASH_CANCEL_BTN = (By.XPATH, "//div[@x-show='showPaymentModal']//button[contains(., 'Batal')]")

    # Qty +/- buttons
    MINUS_QTY_BTN = (By.XPATH, "//aside//button[contains(@class, 'rounded-md') and .//i[contains(@class, 'fa-minus')]]")
    PLUS_QTY_BTN = (By.XPATH, "//aside//button[contains(@class, 'rounded-md') and .//i[contains(@class, 'fa-plus')]]")

    def open(self, base_url):
        self.navigate_to(f"{base_url}/pos")

    def get_product_cards(self):
        return self.find_all(self.PRODUCT_CARDS)

    def add_first_product(self, times=1):
        cards = self.get_product_cards()
        if cards:
            for _ in range(times):
                cards[0].click()

    def add_product_by_index(self, index=0, times=1):
        cards = self.get_product_cards()
        if len(cards) > index:
            for _ in range(times):
                cards[index].click()

    def get_cart_item_rows(self):
        try:
            return self.driver.find_elements(*self.CART_ROWS)
        except Exception:
            return []

    def decrease_first_item_qty(self):
        btn = self.wait.until(EC.element_to_be_clickable(self.MINUS_QTY_BTN))
        btn.click()

    def open_cash_modal(self):
        btn = self.wait.until(EC.element_to_be_clickable(self.CASH_PAY_BUTTON))
        btn.click()

    def is_cash_modal_open(self):
        return self.is_visible(self.CASH_MODAL)

    def input_cash_amount(self, amount):
        el = self.find(self.CASH_INPUT)
        el.clear()
        el.send_keys(str(amount))

    def confirm_cash_payment(self):
        btn = self.wait.until(EC.element_to_be_clickable(self.CASH_CONFIRM_BTN))
        btn.click()

    def click_qris_button(self):
        btn = self.wait.until(EC.element_to_be_clickable(self.QRIS_PAY_BUTTON))
        btn.click()

    def is_cancel_qris_displayed(self):
        return self.is_visible(self.CANCEL_QRIS_BUTTON)

    def cancel_qris_payment(self):
        btn = self.wait.until(EC.element_to_be_clickable(self.CANCEL_QRIS_BUTTON))
        btn.click()
