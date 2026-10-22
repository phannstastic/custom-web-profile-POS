from selenium.webdriver.common.by import By
from .base_page import BasePage

class PosPage(BasePage):
    # Locators
    PRODUCT_CARDS = (By.CSS_SELECTOR, ".grid div[x-on\\:click*='addToCart'], .grid div[@click*='addToCart']")
    CART_ITEMS = (By.CSS_SELECTOR, "div[x-show*='cart'] .space-y-3 > div, aside .space-y-2 > div")
    TOTAL_PRICE_LABEL = (By.CSS_SELECTOR, "span.font-mono-num")
    CASH_PAY_BUTTON = (By.XPATH, "//button[contains(., 'Bayar Tunai') or contains(., 'Cash')]")
    QRIS_PAY_BUTTON = (By.XPATH, "//button[contains(., 'Pembayaran QRIS') or contains(., 'QRIS')]")

    def open(self, base_url):
        self.navigate_to(f"{base_url}/pos")

    def click_first_product(self):
        cards = self.find_all(self.PRODUCT_CARDS)
        if cards:
            cards[0].click()

    def get_cart_items_count(self):
        try:
            items = self.driver.find_elements(*self.CART_ITEMS)
            return len(items)
        except Exception:
            return 0
