from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from .base_page import BasePage

class AdminSettingsPage(BasePage):
    # Locators
    STORE_NAME_INPUT = (By.NAME, "store_name")
    HERO_BADGE_INPUT = (By.NAME, "hero_badge")
    HERO_TITLE_PREFIX_INPUT = (By.NAME, "hero_title_prefix")
    STORE_DESC_INPUT = (By.NAME, "store_description")
    SUBMIT_BTN = (By.CSS_SELECTOR, "form[action*='/admin/settings/update'] button[type='submit']")

    def open(self, base_url):
        self.navigate_to(f"{base_url}/admin/settings")

    def update_narrative(self, store_name=None, badge=None, description=None):
        if store_name:
            self.type(self.STORE_NAME_INPUT, store_name)
        if badge:
            self.type(self.HERO_BADGE_INPUT, badge)
        if description:
            self.type(self.STORE_DESC_INPUT, description)

    def submit_settings(self):
        btn = self.wait.until(EC.element_to_be_clickable(self.SUBMIT_BTN))
        btn.click()
