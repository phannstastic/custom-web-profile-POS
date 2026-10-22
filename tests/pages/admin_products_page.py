from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from .base_page import BasePage

class AdminProductsPage(BasePage):
    # Locators
    ADD_MENU_BTN = (By.XPATH, "//button[contains(., 'Tambah Menu Baru')]")
    MODAL_FORM = (By.CSS_SELECTOR, "div[x-show='showModal']")
    NAME_INPUT = (By.NAME, "name")
    PRICE_INPUT = (By.NAME, "price")
    CATEGORY_SELECT = (By.NAME, "category_id")
    DESCRIPTION_INPUT = (By.NAME, "description")
    SUBMIT_BTN = (By.CSS_SELECTOR, "form[action*='/admin/products'] button[type='submit']")
    PRODUCT_TABLE_ROWS = (By.CSS_SELECTOR, "table tbody tr")

    def open(self, base_url):
        self.navigate_to(f"{base_url}/admin/products")

    def click_add_menu(self):
        btn = self.wait.until(EC.element_to_be_clickable(self.ADD_MENU_BTN))
        btn.click()

    def is_modal_open(self):
        return self.is_visible(self.MODAL_FORM)

    def fill_form(self, name, price, description=""):
        self.type(self.NAME_INPUT, name)
        self.type(self.PRICE_INPUT, str(price))
        if description:
            self.type(self.DESCRIPTION_INPUT, description)

    def submit_product(self):
        btn = self.wait.until(EC.element_to_be_clickable(self.SUBMIT_BTN))
        btn.click()
